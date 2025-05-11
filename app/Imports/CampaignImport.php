<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Source;
use App\Models\Lead;
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
     * Process the uploaded file using PhpSpreadsheet.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function importLeads($file)
    {
        try {
            $filePath = $file->getRealPath();

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true); // Include headers
            // dd($rows);

            $data = Source::findOrFail($this->source_id);

            foreach ($rows as $key => $row) {
                // Skip the header row
                if ($key === 1) {
                    continue;
                }
                // Extract data or set defaults
                $fillable = [
                    'user_id' => auth()->user()->id,
                    'source_id' => $this->source_id,
                    'company_name' => $row['A'] ?? '',
                    'prospect_first_name' => $row['C'] ?? '',
                    'prospect_last_name' => $row['D'] ?? '',
                    'prospect_email' => $row['I'] ?? '',
                    'contact_number_1' => $row['G'] ?? '',
                    'location' => $row['L'] ?? '',
                    'timezone' => $row['M'] ?? '',
                    'asign_to_manager' => $data->assign_to_manager,
                    'company_industry' => $row['B'] ?? '',
                    'designation' => $row['E'] ?? '',
                    'linkedin_address' => $row['J'] ?? '',
                    'bussiness_function' => $row['K'] ?? '',
                    'contact_number_2' => $row['H'] ?? '',
                    'designation_level' => $row['F'] ?? '',
                    'date_shared' => isset($row['N']) ? date('Y-m-d', strtotime($row['N'])) : '',
                ];

                // Check if the lead already exists
                if (!$fillable['linkedin_address']) {
                    Lead::create($fillable);
                } else {
                    $exists = Lead::where([
                        ['linkedin_address', $fillable['linkedin_address']],
                        ['source_id', $fillable['source_id']]
                    ])->exists();

                    if (!$exists) {
                        Lead::create($fillable);
                    } else {
                        $this->appendToCsvFile($this->source_id, $fillable, $key - 2); // Adjust index
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Append unimported leads to a CSV file.
     *
     * @param int $sourceID
     * @param array $data
     * @param int $index
     */
    private function appendToCsvFile(int $sourceID, $data, $index)
    {
        try {
            $leadNotImported = LeadNotImported::firstOrCreate(
                [
                    'source_id' => $sourceID,
                ],
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

            $filePath = storage_path("app/public/leads_not_imported/" . $leadNotImported->file_name);

            if ($index === 0) {
                $fileHandle = fopen($filePath, 'w');
                if ($fileHandle) {
                    $headers = [
                        'User Id', 'Source Id', 'Company Name', 'Prospect First Name', 'Prospect Last Name',
                        'Prospect Email', 'Contact Number 1', 'Location', 'Timezone', 'Asign To Manager',
                        'Company Industry', 'Designation', 'Linkedin Address', 'Bussiness Function',
                        'Contact Number 2', 'Designation Level', 'Date Shared'
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
            Log::error('Message: ' . $e->getMessage());
        }
    }
}
