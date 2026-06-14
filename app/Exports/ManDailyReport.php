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
    protected $filter_by;
    protected $reminder_for_conversation;
    protected $onlyConversation;

    function __construct($camp_id, $emp_id, $date_from, $date_to, $filter_by = null, $reminder_for_conversation = null, $onlyConversation = null)
    {
        $this->camp_id = $camp_id;
        $this->emp_id = $emp_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->filter_by = $filter_by;
        $this->reminder_for_conversation = $reminder_for_conversation;
        $this->onlyConversation = $onlyConversation;
    }

    // Query function (your custom query logic)
    public function query()
    {
        $query = Lead::select(
            'users.name as name',
            'sources.source_name as source_name',
            'sources.description as description',
            'leads.company_name as company_name',
            'leads.prospect_first_name',
            'leads.prospect_last_name',
            'leads.designation',
            'leads.linkedin_address',
            'notes.feedback',
            'notes.reminder_for',
            'leads.note_created_date',
            'notes.phone_number'
        )
        ->join('notes', 'notes.lead_id', '=', 'leads.id')
        ->join('users', 'users.id', '=', 'leads.asign_to')
        ->join('sources', 'sources.id', '=', 'leads.source_id');

        if ($this->emp_id != "") {
            $query->where('leads.asign_to', (string)$this->emp_id);
        }

        if ($this->camp_id != "") {
            $query->where('notes.source_id', (string)$this->camp_id);
        }

        if ($this->date_from != "" && $this->date_to != "") {
            $date_from_new = date('Y-m-d H:i:s', strtotime($this->date_from));
            $date_to_new = date('Y-m-d H:i:s', strtotime($this->date_to));
            $query->whereBetween('notes.updated_at', [$date_from_new, $date_to_new]);
        }

        // Apply VM/No Response or Conversation filters
        if ($this->onlyConversation) {
            $query->whereNotNull('notes.reminder_for');
        } elseif ($this->filter_by != "") {
            if ($this->filter_by == 1) {
                $query->whereNull('notes.reminder_for');
            } elseif ($this->filter_by == 2) {
                if ($this->reminder_for_conversation != "") {
                    $query->where('notes.reminder_for', $this->reminder_for_conversation);
                } else {
                    $query->whereNotNull('notes.reminder_for');
                }
            }
        }

        $query->orderBy('notes.updated_at', 'desc');

        return $query;
    }

    // Map data to the rows of the Excel
    public function map($lead): array
    {
        $dateStr = '';
        $timeStr = '';
        if ($lead->note_created_date) {
            $ts = strtotime($lead->note_created_date);
            if ($ts !== false) {
                $dateStr = date('d/m/Y', $ts);
                $timeStr = date('h:i a', $ts);
            }
        }

        return [
            $lead->name,
            $lead->source_name,
            $lead->description,
            $lead->company_name,
            $lead->prospect_first_name . ' ' . $lead->prospect_last_name,
            $lead->designation,
            $lead->linkedin_address,
            !empty(trim($lead->feedback ?? '')) ? $lead->feedback : 'N/A',
            !empty($lead->reminder_for) ? $lead->reminder_for : 'N/A',
            $dateStr,
            $timeStr,
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
