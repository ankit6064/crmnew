<?php

namespace App\Http\Controllers;

use App\Exports\DailyReport;
use App\Exports\ManDailyReport;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Mail\DailyReport as DailyReportEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DailyReportController extends Controller
{
    public function daily_report(Request $request, $id)
    {
        if (request()->get('campaign_id')) {
            $campaign_id =  $_GET['campaign_id'];
        } else {
            $campaign_id =  "";
        }
        if(request()->get('date_from')){
            $date_from =  $_GET['date_from']; 
        }else{
            $date_from =  "";
        }
        if(request()->get('date_to')){
            $date_to =  $_GET['date_to']; 
        }else{
            $date_to =  "";
        }
    	if (request()->get('filter_by')) {
            $filter_by =  $_GET['filter_by'];
        } else {
            $filter_by =  "";
        }
        if (request()->get('reminder_for_conversation')) {
            $reminder_for_conversation =  $_GET['reminder_for_conversation'];
        } else {
            $reminder_for_conversation =  "";
        }

        $report = new DailyReport($request->id, $campaign_id, $date_from, $date_to, $filter_by, $reminder_for_conversation);
        $filePath = $report->export();

    }
    public function man_daily_report(Request $request)
    {
        // Get input values from the request
        $camp_id = request()->get('campaign_id', '');
        $emp_id = request()->get('employee_id', '');
        $date_from = request()->get('date_from', '');
        $date_to = request()->get('date_to', '');
        $filter_by = request()->get('filter_by', '');
        $reminder_for_conversation = request()->get('reminder_for_conversation', '');
    
        // Set memory and execution time limits
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
    
        // If both campaign and employee IDs are provided and dates are not provided
        if ($camp_id != "" && $emp_id != "" && $date_from == "" && $date_to == "") {
            $name = Source::query()->where("id", '=', $camp_id)->get();
            foreach ($name as $data) {
                $source_name = $data['source_name'];
            }
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    
        // If only employee ID is provided and dates are not provided
        elseif ($camp_id == "" && $emp_id != "" && $date_from == "" && $date_to == "") {
            $employee = User::where('id', '=', $emp_id)->first();
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    
        // If only campaign ID is provided and dates are not provided
        elseif ($emp_id == "" && $camp_id != "" && $date_from == "" && $date_to == "") {
            $camp_name = Source::where('id', '=', $camp_id)->first();
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    
        // If neither campaign ID nor employee ID is provided, but no dates are provided
        elseif ($camp_id == "" && $emp_id == "" && $date_from == "" && $date_to == "") {
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    
        // If employee ID is provided and dates are provided
        elseif ($camp_id == "" && $emp_id != "" && $date_from != "" && $date_to != "") {
            $employee = User::where('id', '=', $emp_id)->first();
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    
        // If both campaign ID and employee ID are provided and dates are provided
        elseif ($camp_id != "" && $emp_id != "" && $date_from != "" && $date_to != "") {
            $camp_name = Source::where('id', '=', $camp_id)->first();
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    
        // If only date range is provided
        elseif ($date_from && $date_to) {
            return $exportedFilePath = (new ManDailyReport($camp_id, $emp_id, $date_from, $date_to, $filter_by, $reminder_for_conversation))->export();
            // return response()->download($exportedFilePath);
        }
    }
    
    public function dailyReportForScheduler(){
        try {
           
            // Log::debug("Cron start at: " . date("d-m-Y h:i:sa"));
            DB::table('cron')->insert(
                [
                    'value' => "cron start",
                    'created_at' => Carbon::now()->toDateTimeString()
                ]);
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 3000);      
            $toDate = Carbon::now()->setTime(06, 00, 00)->toDateTimeString();
            $today = \Carbon\Carbon::now()->englishDayOfWeek;
            $subDay = 1;
            if(ucfirst($today == "monday")){
                $subDay = 7;
            }
            $fromDate = Carbon::now()->subDays($subDay)->setTime(06, 00, 00)->toDateTimeString();  //->subDays(1)
            $filePath = "daily_report"; //.date("-d-m-Y");
            if(!File::exists("storage/app/public/".$filePath)) {
                File::makeDirectory("storage/app/public/".$filePath, $mode = 0777, true, true); 
            }
                             
            $fileName = "Daily_Report" . date("-d-m-Y").'_'.time(). '.xlsx'; //'_'.time()           
            $storeExcel =  Excel::store(new ManDailyReport(null, null, $fromDate, $toDate, 2, null, true),  "public/".$filePath."/".$fileName);          
              DB::table('cron')->insert(
                    [
                        'value' => "file gen",
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]);
            if($storeExcel){
                 $mailContent = [                
                    "subject" =>  'Daily_Report' . date("-d-m-Y"),               
                    "filePath" =>   $filePath,
                    "fileName" =>   $fileName,
                ];
                // Send as attachment            
                $to = "amrinder.d@revvlocity.com";
                Mail::to($to) 
                    ->send(new DailyReportEmail($mailContent));
                DB::table('cron')->insert(
                    [
                        'value' => "mail sent",
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]);
                // Log::debug("Cron mail must sent at: " . date("d-m-Y h:i:sa"));
            }
        }
        //catch exception
        catch(Exception $e) {
          $message = 'Cron Message: ' .$e->getMessage();
          // Log::debug($message);      
        }
        
    }
}
