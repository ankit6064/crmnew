<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Source;
use App\Models\Lead;
use App\Http\Requests\ImportRequest;
use App\Imports\CampaignImport;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\HeadingRowImport;
use DB;
use Illuminate\Support\Facades\Storage;


class ImportExportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('downloadCsv');
    }

    public function importLeads()
    {
        return view('importexport');
    }

    public function import(ImportRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'source_name' => 'required',
            'description' => 'required',
        ]);
        if ($validator->passes()) {
            $data = array(
                'user_id' => auth()->user()->id,
                'source_name' => $request->source_name,
                'description' => $request->description,
                'start_date' => isset($request->start_date) && !empty($request->start_date) ? date('Y-m-d', strtotime($request->start_date)) : NULL,
                'end_date' => isset($request->end_date) && !empty($request->end_date) ? date('Y-m-d', strtotime($request->end_date)) : NULL
            );
            $source_ids = Source::create($data)->id;
            $file = request()->file('file');
            $headings = (new HeadingRowImport())->toArray($file);
            if ($headings[0][0][0] != "company_name") {
                return Redirect::back()->with('error', "'company_name' hearder name is incorrect please check your CSV file");
            } elseif ($headings[0][0][1] != "company_industry") {
                return Redirect::back()->with('error', "'company_industry' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][2] != "prospect_first_name") {
                return Redirect::back()->with('error', "'prospect_first_name' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][3] != "prospect_last_name") {
                return Redirect::back()->with('error', "'prospect_last_name' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][4] != "designation") {
                return Redirect::back()->with('error', "'designation' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][5] != "designation_level") {
                return Redirect::back()->with('error', "'designation_level' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][6] != "contact_number_1") {
                return Redirect::back()->with('error', "'contact_number_1' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][7] != "contact_number_2") {
                return Redirect::back()->with('error', "'contact_number_2' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][8] != "prospect_email") {
                return Redirect::back()->with('error', "'prospect_email' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][9] != "linkedin_address") {
                return Redirect::back()->with('error', "'linkedin_address' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][10] != "bussiness_function") {
                return Redirect::back()->with('error', "'bus0siness_function' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][11] != "location") {
                return Redirect::back()->with('error', "'location' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][12] != "timezone") {
                return Redirect::back()->with('error', "'timezone' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][13] != "date_shared") {
                return Redirect::back()->with('error', "'date_shared' hearder name is incorrect please check your CSV file");

            }
            $path = $request->file('file')->getRealPath();
            $row = 1;
            if (($handle = fopen($path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $fillable = array();
                    if ($row != 1) {
                        $companyName = preg_replace('/[-?]/', '', mb_convert_encoding($data[0], 'UTF-8', 'UTF-8'));
                        $companyName = trim($companyName);
                        if (isset($data[6]) && !empty($data[6])) {
                            $contact_number_1 = explode('/', $data[6]);
                        } else {
                            $contact_number_1 = array();
                        }
                        if (isset($data[7]) && !empty($data[7])) {
                            $contact_number_2 = explode('/', $data[7]);
                        } else {
                            $contact_number_2 = array();
                        }
                        // $fillable[$row] = array(
                        //     'user_id' => auth()->user()->id,
                        //     'source_id' => $source_ids,
                        //     'company_name' => isset($companyName) && !empty($companyName) ? $companyName : 'Not Assigned',
                        //     'prospect_first_name' => isset($data[2]) && !empty($data[2]) ? $data[2] : '',
                        //     'prospect_last_name' => isset($data[3]) && !empty($data[3]) ? $data[3] : '',
                        //     'prospect_email' => isset($data[8]) && !empty($data[8]) ? $data[8] : '',
                        //     'contact_number_1' => isset($data[6]) && !empty($data[6]) ? $data[6] : '',
                        //     'location' => isset($data[11]) && !empty($data[11]) ? $data[11] : '',
                        //     'timezone' => isset($data[12]) && !empty($data[12]) ? $data[12] : '',
                        //     'company_industry' => isset($data[1]) && !empty($data[1]) ? $data[1] : '',
                        //     'designation' => isset($data[4]) && !empty($data[4]) ? $data[4] : '',
                        //     'linkedin_address' => isset($data[9]) && !empty($data[9]) ? $data[9] : '',
                        //     'bussiness_function' => isset($data[10]) && !empty($data[10]) ? $data[10] : '',
                        //     'contact_number_2' => isset($data[7]) && !empty($data[7]) ? $data[7] : '',
                        //     'designation_level' => isset($data[5]) && !empty($data[5]) ? $data[5] : '',
                        //     'date_shared' => isset($data[13]) && !empty($data[13]) ? date('Y-m-d', strtotime($data[13])) : '',
                        //     'created_at' => date('Y-m-d H:i:s'),
                        //     'updated_at' => date('Y-m-d H:i:s'),
                        // );
                        Lead::create([
                            'user_id' => auth()->user()->id,
                            'source_id' => $source_ids,
                            'company_name' => isset($companyName) && !empty($companyName) ? $companyName : 'Not Assigned',
                            'prospect_first_name' => isset($data[2]) && !empty($data[2]) ? $data[2] : '',
                            'prospect_last_name' => isset($data[3]) && !empty($data[3]) ? $data[3] : '',
                            'prospect_email' => isset($data[8]) && !empty($data[8]) ? $data[8] : '',
                            'contact_number_1' => isset($data[6]) && !empty($data[6]) ? $data[6] : '',
                            'location' => isset($data[11]) && !empty($data[11]) ? $data[11] : '',
                            'timezone' => isset($data[12]) && !empty($data[12]) ? $data[12] : '',
                            'company_industry' => isset($data[1]) && !empty($data[1]) ? $data[1] : '',
                            'designation' => isset($data[4]) && !empty($data[4]) ? $data[4] : '',
                            'linkedin_address' => isset($data[9]) && !empty($data[9]) ? $data[9] : '',
                            'bussiness_function' => isset($data[10]) && !empty($data[10]) ? $data[10] : '',
                            'contact_number_2' => isset($data[7]) && !empty($data[7]) ? $data[7] : '',
                            'designation_level' => isset($data[5]) && !empty($data[5]) ? $data[5] : '',
                            'date_shared' => isset($data[13]) && !empty($data[13]) ? date('Y-m-d', strtotime($data[13])) : '',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        // if (!$fillable[$row]['linkedin_address']) {
                        //     Lead::create($fillable[$row]);
                        // } else {
                        //     // Lead::chunk(500, function ($leads) use ($fillable, $row) {
                        //     //     foreach ($leads as $lead) {
                        //     //         Lead::firstOrCreate(
                        //     //             [
                        //     //                 'linkedin_address' => $fillable[$row]['linkedin_address'],
                        //     //                 'source_id' => $fillable[$row]['source_id']
                        //     //             ],
                        //     //             $fillable[$row]
                        //     //         );
                        //     //     }
                        //     // });
                        //     Lead::firstOrCreate(
                        //         [
                        //             'linkedin_address' => $fillable[$row]['linkedin_address'],
                        //             'source_id' => $fillable[$row]['source_id']
                        //         ],
                        //         $fillable[$row]
                        //     );
                        // }

                    }
                    $row++;
                }
                fclose($handle);
            }
            // $leads = $this->uniqueArrayByKey($fillable, 'linkedin_address');
        }
        $message = "Campaign Imported Successfully.";
        // if (!empty($leads['duplicate'])) {
        //     $message .= " The following records were not imported due to duplication.";
        //     $message .= "<br>";
        //     foreach ($leads['duplicate'] as $key => $duplicate) {
        //         $message .= '* '.$duplicate['linkedin_address'];
        //         $message .= '<br>';
        //     }
        // }
        return redirect('sources')->with('success', $message);
    }

    private function uniqueArrayByKey($array, $keyname)
    {
        $result = array();
        $duplicate = array();
        foreach ($array as $key => $val) {
            // check $keyname set in $result array or not
            if (!isset($result[$val[$keyname]])) {
                // if not set then assign $val to $result array
                $result[$val[$keyname]] = $val;
            } else {
                $duplicate[$val[$keyname]] = $val;
            }
        }
        // Re-indexed the array
        $result['unique'] = array_values($result);
        $result['duplicate'] = array_values($duplicate);
        return $result;
    }

    public function import_leads(Request $request)
    {
        // dd($request->source_name);
        $source_id = $request->source_name;
        //$data = Source::where('id')->first();
        $file = request()->file('file');
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $headings = (new HeadingRowImport())->toArray($file);
            if ($headings[0][0][0] != "company_name") {
                return Redirect::back()->with('error', "'company_name' hearder name is incorrect please check your CSV file");
            } elseif ($headings[0][0][1] != "company_industry") {
                return Redirect::back()->with('error', "'company_industry' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][2] != "prospect_first_name") {
                return Redirect::back()->with('error', "'prospect_first_name' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][3] != "prospect_last_name") {
                return Redirect::back()->with('error', "'prospect_last_name' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][4] != "designation") {
                return Redirect::back()->with('error', "'designation' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][5] != "designation_level") {
                return Redirect::back()->with('error', "'designation_level' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][6] != "contact_number_1") {
                return Redirect::back()->with('error', "'contact_number_1' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][7] != "contact_number_2") {
                return Redirect::back()->with('error', "'contact_number_2' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][8] != "prospect_email") {
                return Redirect::back()->with('error', "'prospect_email' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][9] != "linkedin_address") {
                return Redirect::back()->with('error', "'linkedin_address' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][10] != "bussiness_function") {
                return Redirect::back()->with('error', "'bussiness_function' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][11] != "location") {
                return Redirect::back()->with('error', "'location' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][12] != "timezone") {
                return Redirect::back()->with('error', "'timezone' hearder name is incorrect please check your CSV file");

            } elseif ($headings[0][0][13] != "date_shared") {
                return Redirect::back()->with('error', "'date_shared' hearder name is incorrect please check your CSV file");

            }

            Excel::import(new CampaignImport($source_id), $file);
        }
        return redirect('leads/assign_lead_emp/' . $source_id)->with('success', 'Lead Imported Successfully.');
    }

    public function downloadCsv($fileName)
    {
        $filePath = 'public/leads_not_imported/' . $fileName;
        return Storage::download($filePath);
    }
}