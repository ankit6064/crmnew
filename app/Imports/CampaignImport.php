<?php

namespace App\Imports;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Source;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadNotImported;
use Illuminate\Support\Str;

class CampaignImport
{
    protected $source_id;

    public function __construct($source_id)
    {
        $this->source_id = $source_id;
    }

    /**
     * Clean value and force UTF-8
     */
    private function cleanValue($value)
    {
        if (is_null($value)) {
            return '';
        }

        $value = trim($value);

        return mb_convert_encoding(
            $value,
            'UTF-8',
            'UTF-8, ISO-8859-1, ISO-8859-15, Windows-1252'
        );
    }

    /**
     * Import Leads
     */
    public function importLeads($file)
    {
        try {

            $filePath = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());

            /*
            |--------------------------------------------------------------------------
            | Detect File Type Properly (Fix Special Characters Issue)
            |--------------------------------------------------------------------------
            */

            if ($extension === 'csv') {

                $reader = IOFactory::createReader('Csv');
                $reader->setDelimiter(',');
                $reader->setEnclosure('"');
                $reader->setSheetIndex(0);

                // Most Excel CSV files are CP1252 (Windows encoding)
                $reader->setInputEncoding('CP1252');

            } else {

                $reader = IOFactory::createReaderForFile($filePath);
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);

            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $data = Source::findOrFail($this->source_id);

            foreach ($rows as $key => $row) {

                if ($key === 1) {
                    continue; // Skip header
                }

                $fillable = [
                    'user_id' => auth()->user()->id,
                    'source_id' => $this->source_id,
                    'company_name' => $this->cleanValue($row['A'] ?? ''),
                    'prospect_first_name' => $this->cleanValue($row['C'] ?? ''),
                    'prospect_last_name' => $this->cleanValue($row['D'] ?? ''),
                    'prospect_email' => $this->cleanValue($row['I'] ?? ''),
                    'contact_number_1' => $this->cleanValue($row['G'] ?? ''),
                    'location' => $this->cleanValue($row['L'] ?? ''),
                    'timezone' => $this->cleanValue($row['M'] ?? ''),
                    'asign_to_manager' => $data->assign_to_manager,
                    'company_industry' => $this->cleanValue($row['B'] ?? ''),
                    'designation' => $this->cleanValue($row['E'] ?? ''),
                    'linkedin_address' => $this->cleanValue($row['J'] ?? ''),
                    'bussiness_function' => $this->cleanValue($row['K'] ?? ''),
                    'contact_number_2' => $this->cleanValue($row['H'] ?? ''),
                    'designation_level' => $this->cleanValue($row['F'] ?? ''),
                    'date_shared' => isset($row['N']) && !empty($row['N'])
                        ? date('Y-m-d', strtotime($row['N']))
                        : null,
                ];

                /*
                |--------------------------------------------------------------------------
                | Check Duplicate Leads
                |--------------------------------------------------------------------------
                */

                if (empty($fillable['linkedin_address']) || $fillable['linkedin_address'] === '-') {

                    Lead::create($fillable);

                } else {

                    $exists = Lead::where([
                        ['linkedin_address', $fillable['linkedin_address']],
                        ['source_id', $fillable['source_id']]
                    ])->first();

                    if (!$exists) {

                        Lead::create($fillable);

                    } else {

                        $userdetails = User::find($exists->asign_to);

                        $fillable['employee_name'] = $userdetails
                            ? $this->cleanValue($userdetails->first_name . ' ' . $userdetails->last_name)
                            : '';

                        $this->appendToCsvFile($this->source_id, $fillable, $key - 2);
                    }
                }
            }

        } catch (\Exception $e) {

            Log::error('Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Append Unimported Leads to CSV (UTF-8 Safe for Excel)
     */
    private function appendToCsvFile(int $sourceID, $data, $index)
    {
        try {

            $leadNotImported = LeadNotImported::firstOrCreate(
                ['source_id' => $sourceID],
                [
                    'user_id' => auth()->user()->id,
                    'file_name' => Str::uuid() . ".csv",
                ]
            );

            $directoryName = "leads_not_imported";
            $disk = 'public';

            if (!Storage::disk($disk)->exists($directoryName)) {
                Storage::disk($disk)->makeDirectory($directoryName);
            }

            $filePath = storage_path("app/public/{$directoryName}/" . $leadNotImported->file_name);

            /*
            |--------------------------------------------------------------------------
            | Write Header Once + Add UTF-8 BOM (Fix Excel Special Characters)
            |--------------------------------------------------------------------------
            */

            if ($index === 0) {

                $fileHandle = fopen($filePath, 'w');

                if ($fileHandle) {

                    // Add UTF-8 BOM for Excel
                    fwrite($fileHandle, "\xEF\xBB\xBF");

                    $headers = [
                        'User Id',
                        'Source Id',
                        'Company Name',
                        'Prospect First Name',
                        'Prospect Last Name',
                        'Prospect Email',
                        'Contact Number 1',
                        'Location',
                        'Timezone',
                        'Asign To Manager',
                        'Company Industry',
                        'Designation',
                        'Linkedin Address',
                        'Bussiness Function',
                        'Contact Number 2',
                        'Designation Level',
                        'Date Shared',
                        'Assigned Employee'
                    ];

                    fputcsv($fileHandle, $headers);
                    fclose($fileHandle);
                }
            }

            $fileHandle = fopen($filePath, 'a');

            if ($fileHandle) {

                fputcsv($fileHandle, array_values($data));
                fclose($fileHandle);
                chmod($filePath, 0777);
            }

        } catch (\Exception $e) {

            Log::error('CSV Write Failed: ' . $e->getMessage());
        }
    }
}
