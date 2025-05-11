<?php

namespace App\Http\Controllers;

use App\Exports\LeadClosedExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Lead;
use App\Models\LhsReport;
use App\Models\Source;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use HTML_TO_DOC;
use Illuminate\Support\Facades\File;
use Symfony\Component\DomCrawler\Crawler;

class LeadClosedController extends Controller
{


    public function generatePDF()
    {
        // Load library
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();

        $datas = Lead::where('status', 3)->with('lhsreport')->get();
        foreach ($datas as $data) {
            if (!empty($data['lhsreport']->lead_id)) {
                $view =   view("lhs_report")->with(['data' => $data]);
                $firstname = $data['prospect_first_name'];
                $lastname = $data['prospect_last_name'];
                $htd->createDoc("$view", "storage/app/public/Excel".date("-d-m-Y")."/word/$firstname$lastname");
                // $htd->createDoc("$view", "$firstname$lastname", 1);
            }
            // $pdf = PDF::loadView('leadClosed', $data1);
            // Storage::put("public/" . "Excel"  . "/pdf/"  . $firstname . $lastname  . date("-d-m-Y") . ".pdf", $pdf->output());
        }
        /*       Zip Downloader           */
        Storage::disk('local')->makeDirectory('tobedownload', $mode = 0775); // zip store here
        $zip_file = storage_path('app/tobedownload/' . 'leadClosed' . date("-d-m-Y") . '.zip');
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $path = storage_path("app/public/Excel".date("-d-m-Y")."/word/"); // path to your pdf files
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($path) + 0);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        $headers = array('Content-Type' => 'application/octet-stream',);
        $zip_new_name = "leadClosed" . date("-d-m-Y") . ".zip";
        return response()->download($zip_file, $zip_new_name, $headers);
        return redirect('lead/export_excel_pdf');
    }
    public function generateSinglePDF($id)
    {
        // Initialize PHPWord
        include_once 'HtmlToDoc.class.php';

        // Initialize class
        $htd = new HTML_TO_DOC();
        
        // Fetch leads with status 3
        $datas = Lead::where('status', 3)
            ->where("source_id", $id)
            ->with('lhsreport')
            ->get();
        
        // Get source name
        $source = Source::find($id);
        $source_name = $source ? $source->source_name : 'Unknown';
        
        // Define storage path
        $path = storage_path("app/public/Excel".date("-d-m-Y")."/" . $source_name.'/');
        File::makeDirectory($path, $mode = 0777, true, true);
        if(!empty($datas)){
        // Generate Word documents
        foreach ($datas as $data) {
            if ($data['status'] == 3 && !empty($data->lhsreport->lead_id)) {
                // Render the vie

                $view =   view("lhs_report")->with(['data' => $data]);
                $firstname = $data['prospect_first_name'];
                $lastname = $data['prospect_last_name'];
                $filename = $firstname . $lastname . date("-d-m-Y");
                File::makeDirectory($path, $mode = 0777, true, true);
                $htd->createDoc( "$view", $path . $filename );
            }
        }
    }else{
        echo '<script>alert("Word not found"); </script>';
            return Redirect::back()->with('error', "Word Not Found");
    }

        
        
        // Return success message
        
    
        if (!empty($data) && $data['status'] == 3 && !empty($data['lhsreport']->lead_id)) {
            /* Zip Downloader */
            Storage::disk('local')->makeDirectory('tobedownload', $mode = 0775); // zip store here
            $zip_file = storage_path('app/tobedownload/' . $source_name . date("-d-m-Y") . '.zip');
            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $path = storage_path("app/public/Excel".date("-d-m-Y")."/" . $source_name . "/"); // path to your Word files
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $name => $file) {
                // Skipping all subfolders
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Extracting filename with substr/strlen
                    $relativePath = substr($filePath, strlen($path) + 0);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        } else {
            echo '<script>alert("Word not found"); </script>';
            return Redirect::back()->with('error', "Word Not Found");
        }
    
        $zip->close();
        $headers = array('Content-Type' => 'application/octet-stream');
        $zip_new_name = $source_name . date("-d-m-Y") . ".zip";
        return response()->download($zip_file, $zip_new_name, $headers);
    }

    /*  single pdf download  */
    public function singlepdfdown($id)
    {
        // Load library
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();
        // if (request()->get('camp_id')) {
        //     $camp_id =  $_GET['camp_id'];
        // } else {
        //     $camp_id =  "";
        // }
        $both = Lead::where('id', '=', $id)->first();
        if (request()->get('emp_id')) {
            $emp_id =  $_GET['emp_id'];
        } else {
            $emp_id =  "";
        }

        /*            Lead data get       */
        $datas = Lead::where('status', '=', 3)
            ->where("id", '=', $id)
            ->with('lhsreport')
            ->get();
        /*       Source name get         */

        Source::all();
        $name =  Source::query()->where("id", '=', $both['source_id'])->first();
        // foreach ($name as $data) {
        //     $data1 = [
        //         'source_name' => $data['source_name'],

        //     ];
        // }
        $source_name =  $name['source_name'];
        LhsReport::all();
        $getalldata = LhsReport::query()->get();
        foreach ($getalldata as $data) {
        }
        $lead_id = $data['lead_id'];
        // dd($lead_id);
        /*      PDF Data Get         */
        foreach ($datas as $data) {
           // if ($data['lhsreport']->lead_id == $data['id']) {
            if ($data['status'] == 3 && !empty($data['lhsreport']->lead_id)) {
                $view =   view("lhs_report")->with(['data' => $data]);
                $firstname = $data['prospect_first_name'];
                $lastname = $data['prospect_last_name'];
                $path = storage_path("app/public/Excel".date("-d-m-Y")."/" . $source_name . ' single ' . $id);
                File::makeDirectory($path, $mode = 0777, true, true);
                $htd->createDoc("$view", "storage/app/public/Excel".date("-d-m-Y")."/" . $source_name . ' single ' . $id . "/" . $firstname . $lastname . date("-d-m-Y"));
                // $pdf = PDF::loadView('leadClosed', $data1);
                // $kasa = Storage::put("public/Excel/"  . $source_name . "/"  . $firstname . $lastname  . date("-d-m-Y") . ".pdf", $pdf->output());
            }
            // $pdf = PDF::loadView('leadClosed', $data1);
            // $kasa = Storage::put("public/Excel/"  . $source_name . ' single ' . $id . "/"  . $firstname . $lastname  . date("-d-m-Y") . ".pdf", $pdf->output());
        }
        /*       Zip Downloader           */
        Storage::disk('local')->makeDirectory('tobedownload', $mode = 0775); // zip store here
        $zip_file = storage_path('app/tobedownload/' . $source_name . date("-d-m-Y") . '.zip');
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $path = storage_path("app/public/Excel".date("-d-m-Y")."/" . $source_name . ' single ' . $id  . "/"); // path to your pdf files
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($path) + 0);
                $zip->addFile($filePath, $relativePath);
            }
        }


        $zip->close();
        $headers = array('Content-Type' => 'application/octet-stream',);
        $zip_new_name = $source_name . date("-d-m-Y") . ".zip";
        return response()->download($zip_file, $zip_new_name, $headers);
        return redirect('leads/export_excel_pdf');
    }

    /*  employee performance pdf download  */
    public function wordEmployeeDownSingle($id)
    {
        // Load library
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();

        /*            Lead data get       */
        $data = Lead::where('status', '=', 3)
            ->where("id", '=', $id)
            ->with('lhsreport')
            ->first();
        
        /* Source name get */
        $name =  Source::query()->where("id", '=', $data['source_id'])->first();
        
        $source_name =  $name['source_name'];
        $lead_id = $data['lead_id'];
        /*      Data Get         */
        if ($data['status'] == 3 && !empty($data['lhsreport']->lead_id)) {
            $view =   view("lhs_report")->with(['data' => $data]);
            $firstname = $data['prospect_first_name'];
            $lastname = $data['prospect_last_name'];
            $path = "storage/app/public/Excel".date("-d-m-Y")."/" . $source_name . ' performance ' .time(). $id . "/";
            $filename = $firstname . $lastname . date("-d-m-Y");
            File::makeDirectory($path, $mode = 0777, true, true);
            $htd->createDoc( "$view", $path . $filename );
            $headers = array('Content-Type' => 'application/octet-stream');

            return response()->download($path.$filename.'.doc', $filename.'.doc', $headers);
        }else{
            return redirect('leads/export_excel_pdf');
        }
    }

    /*  employee performance pdf download  */
    public function wordEmployeeDownSingleMom($id)
    {
        // Load library
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();

        /*            Lead data get       */
        $data = Lead::where('status', '=', 3)
            ->where("id", '=', $id)
            ->with('lhsreport')
            ->first();
        
        /* Source name get */
        $name =  Source::query()->where("id", '=', $data['source_id'])->first();
        
        $source_name =  $name['source_name'];
        $lead_id = $data['lead_id'];
        /*      Data Get         */
        if ($data['status'] == 3 && !empty($data['lhsreport']->lead_id)) {
            $view =   view("lhs_report")->with(['data' => $data]);
            $firstname = $data['prospect_first_name'];
            $lastname = $data['prospect_last_name'];
            $path = "storage/app/public/Excel".date("-d-m-Y")."/" . $source_name . ' performance ' .time(). $id . "/";
            $filename = $firstname . $lastname . date("-d-m-Y");
            File::makeDirectory($path, $mode = 0777, true, true);
            $htd->createDoc( "$view", $path . $filename );
            $headers = array('Content-Type' => 'application/octet-stream');

            return response()->download($path.$filename.'.doc', $filename.'.doc', $headers);
        }else{
            return redirect('leads/export_excel_pdf');
        }
    }

    /*  employee performance pdf download  */
    public function wordEmployeeDown($id)
    {
        // Load library
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();
        // if (request()->get('campaign_id')) {
        //     $campaign_id =  $_GET['campaign_id'];
        // } else {
        //     $campaign_id =  "";
        // }
        // if (request()->get('employee_id')) {
        //     $employee_id =  $_GET['employee_id'];
        // } else {
        //     $employee_id =  "";
        // }
        $both = Lead::where('id', '=', $id)->first();

        /*            Lead data get       */
        $datas = Lead::where('status', '=', 3)
            ->where("id", '=', $id)
            ->with('lhsreport')
            ->get();
        /*       Source name get         */
        Source::all();
        $name =  Source::query()->where("id", '=', $both['source_id'])->first();
        // foreach ($name as $data) {
        //     $data1 = [
        //         'source_name' => $data['source_name'],

        //     ];
        // }
        $source_name =  $name['source_name'];
        LhsReport::all();
        $getalldata = LhsReport::query()->get();
        foreach ($getalldata as $data) {
        }
        $lead_id = $data['lead_id'];
        // dd($lead_id);
        /*      PDF Data Get         */
        foreach ($datas as $data) {
          //  if (!empty($data['lhsreport']->lead_id)) {
            if ($data['status'] == 3 && !empty($data['lhsreport']->lead_id)) {
                $view =   view("lhs_report")->with(['data' => $data]);
                $firstname = $data['prospect_first_name'];
                $lastname = $data['prospect_last_name'];
                $path = storage_path("app/public/Excel".date("-d-m-Y")."/" . $source_name . ' performance '.time() . $id);
                File::makeDirectory($path, $mode = 0777, true, true);
                $htd->createDoc("$view", "storage/app/public/Excel".date("-d-m-Y")."/" . $source_name . ' performance ' .time(). $id . "/" . $firstname . $lastname . date("-d-m-Y"));
                // $pdf = PDF::loadView('leadClosed', $data1);
                // $kasa = Storage::put("public/Excel/"  . $source_name . "/"  . $firstname . $lastname  . date("-d-m-Y") . ".pdf", $pdf->output());
            } 
            // $pdf = PDF::loadView('leadClosed', $data1);
            // $kasa = Storage::put("public/Excel/"  . $source_name . ' single ' . $id . "/"  . $firstname . $lastname  . date("-d-m-Y") . ".pdf", $pdf->output());
        }
        /*       Zip Downloader           */
        Storage::disk('local')->makeDirectory('tobedownload', $mode = 0775); // zip store here
        $zip_file = storage_path('app/tobedownload/' . $source_name . date("-d-m-Y") . '.zip');
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $path = storage_path("app/public/Excel".date("-d-m-Y")."/" . $source_name . ' performance '.time() . $id  . "/"); // path to your pdf files
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($path) + 0);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        $headers = array('Content-Type' => 'application/octet-stream',);
        $zip_new_name = $source_name . date("-d-m-Y") . ".zip";
        return response()->download($zip_file, $zip_new_name, $headers);
        return redirect('leads/export_excel_pdf');
    }
    public function export_excel_pdf()
    {
        return view('exportExcelPdf');
    }
    public function viewpdf()
    {
        return view('leadClosed');
    }

    public function generateDocx(Request $request)
    {
        $phpWord = new \PhpOffice\PhpWord\phpword();
        $section = $phpWord->addSection();
        $html = '
        <table style="width: 50%; border: 2px #000000 solid;">
        <tr>
          <th>Company</th>
          <th>Country</th>
        </tr>
        <tr>
          <td>Alfreds</td>
          <td>Germany</td>
        </tr>
      </table>';
        Html::addHtml($section, $html);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(storage_path('helloWorld.docx'));
        } catch (Exception $e) {
        }
        return response()->download(storage_path('helloWorld.docx'));
    }
    public function newDocx()
    {
        $datas = Lead::where('status', 3)->with('lhsreport')->get();

        // Load library
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();

        $htmlContent = '
            <h1>Hello World!</h1>
            <p>This document is created from HTML.</p>';

        // $htd->createDoc($htmlContent, "my-document", 1);
        foreach ($datas as $data) {
            if (!empty($data['lhsreport']->lead_id)) {
                $view =   view("lhs_report")->with(['data' => $data]);
                $firstname = $data['prospect_first_name'];
                $lastname = $data['prospect_last_name'];
                $htd->createDoc("$view", "$firstname$lastname");
            }

            // $htd->createDoc("$view", "$firstname$lastname", 1);
        }
    }
}
