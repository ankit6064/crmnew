<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Lead;
use Illuminate\Support\Facades\Response;

class ManDailyReport
{
    protected $camp_id;
    protected $emp_id;
    protected $date_from;
    protected $date_to;
    protected $onlyConversation;

    function __construct($camp_id, $emp_id, $date_from, $date_to, $par = null, $par1 = null, $onlyConversation = null)
    {
        $this->camp_id = $camp_id;
        $this->emp_id = $emp_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->onlyConversation = $onlyConversation;
    }

    // Query function (your custom query logic)
    public function query()
    {
        $date_from_new = date('Y-m-d H:i:s', strtotime($this->date_from));
        $date_to_new = date('Y-m-d H:i:s', strtotime($this->date_to));

        if ($this->emp_id != "" && $this->camp_id != "" && $this->date_from == "" && $this->date_to == "") {
            return Lead::query()
                ->where('asign_to', $this->emp_id)
                ->where('notes.source_id', $this->camp_id)
                // ->whereNotNull('notes.source_id')
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.assign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at');
        } elseif ($this->emp_id != "" && $this->camp_id == "" && $this->date_from == "" && $this->date_to == "") {
            return Lead::query()
                ->where('asign_to', $this->emp_id)
                // ->whereNotNull('notes.source_id')
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at');
        } elseif ($this->emp_id == "" && $this->camp_id != "" && $this->date_from == "" && $this->date_to == "") {
            return Lead::query()
                ->where('notes.source_id', '=', $this->camp_id)
                // ->whereNotNull('notes.source_id')
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at');
        } elseif ($this->emp_id == "" && $this->camp_id == "" && $this->date_from == "" && $this->date_to == "") {
            return Lead::join('notes', 'notes.lead_id', '=', 'leads.id')
                // ->whereNotNull('notes.source_id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at');
        } elseif ($this->emp_id != "" && $this->camp_id == "" && $this->date_from != "" && $this->date_to != "") {
            return Lead::query()
                ->where('asign_to', $this->emp_id)
                ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
                // ->whereNotNull('notes.source_id')
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at', 'desc');
        } elseif ($this->emp_id != "" && $this->camp_id != "" && $this->date_from != "" && $this->date_to != "") {
            return Lead::query()
                ->where("notes.source_id", '=', $this->camp_id)
                ->where("asign_to", "=", $this->emp_id)
                // ->whereNotNull('notes.source_id')
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at', 'desc')
                ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new]);
        } elseif ($this->date_from && $this->date_to && !$this->onlyConversation) {
            return Lead::query()
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at', 'desc')
                // ->whereNotNull('notes.source_id')
                ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new]);
        } elseif ($this->date_from && $this->date_to && $this->onlyConversation) {
            return Lead::query()
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->join('users','users.id','=','leads.asign_to')
                ->join('sources','sources.id','=','leads.source_id')
                ->latest('notes.updated_at', 'desc')
                // ->whereNotNull('notes.source_id')
                ->whereNotNull('notes.reminder_for')
                ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new]);
        }
    }

    // Map data to the rows of the Excel
    public function map($lead): array
    {
        return [
            $lead->name,
            $lead->source_name,
            $lead->description,
            $lead->company_name,
            $lead->prospect_first_name . ' ' . $lead->prospect_last_name,
            $lead->designation,
            $lead->linkedin_address,
            $lead->feedback,
            $lead->reminder_for,
            date('d/m/Y', strtotime($lead->note_created_date)),
            date('h:i a', strtotime($lead->note_created_date)),
            $lead->phone_number,
        ];
    }

    // Export function to generate Excel file directly for download
    public function export()
    {
        // Create a new spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set the header row
        $headers = [
            'Employee Name', 'Campaign Name', 'Sub-Campaign Name', 'Organization',
            'Prospect Name', 'Designation', 'LinkedIn', 'Notes', 'Conversation Type',
            'Comment Date', 'Comment Time', 'Note Phone Number'
        ];
    
        // Write the headers to the first row
        $sheet->fromArray($headers, null, 'A1');
    
        // Query data and write it to the sheet
        $data = $this->query()->get(); // Ensure this returns a collection of data
        $rowIndex = 2; // Start from the second row
        foreach ($data as $lead) {
            $sheet->fromArray($this->map($lead), null, 'A' . $rowIndex);
            $rowIndex++;
        }
    
        // Set the sheet style (bold for headers and center alignment)
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // Set auto column width for better readability
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set the writer to output directly to the browser
        $writer = new Xlsx($spreadsheet);

        // Set appropriate headers for download
        $fileName = 'Daily_Report_' . date('d-m-Y') . '.xlsx';

        return Response::stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }
}
