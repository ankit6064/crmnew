<?php

namespace App\Http\Controllers;

use App\Exports\LeadClosedExport;
use App\Exports\LeadClosedExportAll;
use App\Exports\LeadReportSingle;
use App\Models\Lead;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class LeadClosedCsv extends Controller
{
    public function export()
    {

        $storeExcel =  Excel::store(new LeadClosedExportAll, "public/Excel/" . 'leadclosed' . date("-d-m-Y") . '.xlsx');
        return Excel::download(new LeadClosedExportAll, ("leadclosed" . date("-d-m-Y") . ".xlsx"));
    }
    public function reportDown(Request $request, $id)
    {
        return Excel::download(new LeadClosedExport($id), 'LeadClosedExport-' . date('Y-m-d') . '.xlsx');

    }
    
    
    public function reportSingleEmployee()
    {
        if (request()->get('camp_id')) {
            $camp_id =  $_GET['camp_id'];
        } else {
            $camp_id =  "";
        }
        if (request()->get('emp_id')) {
            $emp_id =  $_GET['emp_id'];
        } else {
            $emp_id =  "";
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
        if ($camp_id == "" && $emp_id != "" && $date_from == "" && $date_to == "" ) {
            $employee = User::where('id', '=', $emp_id)->first();
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $employee['name'] . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($employee['name'] . date("-d-m-Y") . ".xlsx"));
        } elseif ($camp_id != "" && $emp_id != "" && $date_from == "" && $date_to == "") {
            Source::all();
            $name =  Source::query()->where("id", '=', $camp_id)->get();
            foreach ($name as $data) {
                $data1 = [
                    'source_name' => $data['source_name'],
                ];
            }
            $source_name =  $data['source_name'];
            // $source_name =  $data['source_name'];
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $source_name . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($source_name . date("-d-m-Y") . ".xlsx"));
        }  
        elseif ($camp_id == "" && $emp_id != "" && $date_from != "" && $date_to != ""){
            $employee = User::where('id', '=', $emp_id)->first();
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $employee['name'] . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle( $camp_id, $emp_id,$date_from,$date_to), ($employee['name'] . date("-d-m-Y") . ".xlsx"));
        }        
        elseif ($camp_id != "" && $emp_id != "" && $date_from != "" && $date_to != ""){
            Source::all();
            $name =  Source::query()->where("id", '=', $camp_id)->get();
            foreach ($name as $data) {
                $data1 = [
                    'source_name' => $data['source_name'],
                ];
            }
            $source_name =  $data['source_name'];
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $source_name . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle( $camp_id, $emp_id,$date_from,$date_to), ($source_name . date("-d-m-Y") . ".xlsx"));
        }
    }
    public function reportEmployeePerformance()
    {
        if (request()->get('campaign_id')) {
            $campaign_id =  $_GET['campaign_id'];
            $camp_id = $campaign_id;
        } else {
            $camp_id =  "";
        }
        if (request()->get('employee_id')) {
            $employee_id =  $_GET['employee_id'];
            $emp_id = $employee_id;
        } else {
            $emp_id =  "";
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
        if ($camp_id != "" && $emp_id != "" &&$date_from == "" && $date_to == "") {
            Source::all();
            $name =  Source::query()->where("id", '=', $campaign_id)->get();
            foreach ($name as $data) {
                $data1 = [
                    'source_name' => $data['source_name'],
                ];
            }
            $source_name =  $data['source_name'];
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $source_name . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($source_name . date("-d-m-Y") . ".xlsx"));
        } 
        elseif ($camp_id == "" && $emp_id != "" &&$date_from == "" && $date_to == "") {
            $employee = User::where('id', '=', $emp_id)->first();
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $employee['name'] . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($employee['name'] . date("-d-m-Y") . ".xlsx"));
        } 
        elseif ($emp_id == "" && $camp_id != "" &&$date_from == "" && $date_to == "") {
            $camp_name = Source::where('id', '=', $camp_id)->first();
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $camp_name['source_name'] . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($camp_name['source_name'] . date("-d-m-Y") . ".xlsx"));
        }
         elseif ($camp_id == "" && $emp_id == "" &&$date_from == "" && $date_to == "") {
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . 'leadclosed' . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ("leadclosed" . date("-d-m-Y") . ".xlsx"));
        } 
        elseif ($camp_id == "" && $emp_id != "" && $date_from != "" && $date_to != ""){
            $employee = User::where('id', '=', $emp_id)->first();
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $employee['name'] . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($employee['name'] . date("-d-m-Y") . ".xlsx"));
        }
        elseif ($camp_id != "" && $emp_id != "" && $date_from != "" && $date_to != ""){
            $camp_name = Source::where('id', '=', $camp_id)->first();
            $storeExcel =  Excel::store(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), "public/Excel".date("-d-m-Y")."/" . $camp_name['source_name'] . date("-d-m-Y") . '.xlsx');
            return Excel::download(new LeadReportSingle($camp_id, $emp_id,$date_from,$date_to), ($camp_name['source_name'] . date("-d-m-Y") . ".xlsx"));
        }
    }
}
