<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Lead;
use App\Models\User;

use Illuminate\Support\Facades\Response;
use Auth;

class ManDailyReport
{
    protected $camp_id;
    protected $emp_id;
    protected $date_from;
    protected $date_to;
    protected $filter_by;
    protected $reminder_for_conversation;
    protected $onlyConversation;

    function __construct($camp_id, $emp_id, $date_from, $date_to, $par = null, $par1 = null, $onlyConversation = null)
    {
        $this->camp_id = $camp_id;
        $this->emp_id = $emp_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->filter_by = $par;
        $this->reminder_for_conversation = $par1;
        $this->onlyConversation = $onlyConversation;
    }

    // Query function (your custom query logic)
    // public function query()
    // {
    //     // dd('test');
    //     $date_from_new = date('Y-m-d H:i:s', strtotime($this->date_from));
    //     $date_to_new = date('Y-m-d H:i:s', strtotime($this->date_to));

    //     if ($this->emp_id != "" && $this->camp_id != "" && $this->date_from == "" && $this->date_to == "") {
    //         // Employee + Campaign filter
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->where('asign_to', $this->emp_id)
    //                 ->where('notes.source_id', $this->camp_id)
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->latest('notes.updated_at');
    //         } else {
    //             return Lead::query()
    //                 ->where('asign_to', $this->emp_id)
    //                 ->where('notes.source_id', $this->camp_id)
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->latest('notes.updated_at');
    //         }

    //     } elseif ($this->emp_id != "" && $this->camp_id == "" && $this->date_from == "" && $this->date_to == "") {
    //         // Employee filter only
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->where('asign_to', $this->emp_id)
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->latest('notes.updated_at');
    //         } else {
    //             return Lead::query()
    //                 ->where('asign_to', $this->emp_id)
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->latest('notes.updated_at');
    //         }

    //     } elseif ($this->emp_id == "" && $this->camp_id != "" && $this->date_from == "" && $this->date_to == "") {
    //         // Campaign filter only
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->where('notes.source_id', '=', $this->camp_id)
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->latest('notes.updated_at');
    //         } else {
    //             return Lead::query()
    //                 ->where('notes.source_id', '=', $this->camp_id)
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->latest('notes.updated_at');
    //         }

    //     } elseif ($this->emp_id == "" && $this->camp_id == "" && $this->date_from == "" && $this->date_to == "") {
    //         // No filter
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->latest('notes.updated_at');
    //         } else {
    //             return Lead::join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->latest('notes.updated_at');
    //         }

    //     } elseif ($this->emp_id != "" && $this->camp_id == "" && $this->date_from != "" && $this->date_to != "") {
    //         // Employee + Date range
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->where('asign_to', $this->emp_id)
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->latest('notes.updated_at', 'desc');
    //         } else {
    //             return Lead::query()
    //                 ->where('asign_to', $this->emp_id)
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->latest('notes.updated_at', 'desc');
    //         }

    //     } elseif ($this->emp_id != "" && $this->camp_id != "" && $this->date_from != "" && $this->date_to != "") {
    //         // Employee + Campaign + Date range
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->where("notes.source_id", '=', $this->camp_id)
    //                 ->where("asign_to", "=", $this->emp_id)
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->latest('notes.updated_at', 'desc');
    //         } else {
    //             return Lead::query()
    //                 ->where("notes.source_id", '=', $this->camp_id)
    //                 ->where("asign_to", "=", $this->emp_id)
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->latest('notes.updated_at', 'desc');
    //         }

    //     } elseif ($this->date_from && $this->date_to && !$this->onlyConversation) {
    //         // Date range only
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->latest('notes.updated_at', 'desc');
    //         } else {
    //             return Lead::query()
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->latest('notes.updated_at', 'desc');
    //         }

    //     } elseif ($this->date_from && $this->date_to && $this->onlyConversation) {
    //         // Only conversations within date range
    //         if (Auth::user()->is_admin == 3) {
    //             $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])
    //                 ->orderBy('id')
    //                 ->pluck('id');

    //             return Lead::query()
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereNotNull('notes.reminder_for')
    //                 ->whereIn('leads.asign_to', $employee_ids)
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->latest('notes.updated_at', 'desc');
    //         } else {
    //             return Lead::query()
    //                 ->join('notes', 'notes.lead_id', '=', 'leads.id')
    //                 ->join('users', 'users.id', '=', 'leads.asign_to')
    //                 ->join('sources', 'sources.id', '=', 'leads.source_id')
    //                 ->whereNotNull('notes.reminder_for')
    //                 ->whereBetween('notes.updated_at', [$date_from_new, $date_to_new])
    //                 ->latest('notes.updated_at', 'desc');
    //         }
    //     }

    // }

    public function query()
    {
        $date_from_new = $this->date_from ? date('Y-m-d H:i:s', strtotime($this->date_from)) : null;
        $date_to_new = $this->date_to ? date('Y-m-d H:i:s', strtotime($this->date_to)) : null;

        $query = Lead::query()
            ->join('notes', 'notes.lead_id', '=', 'leads.id')
            ->join('users', 'users.id', '=', 'leads.asign_to')
            ->join('sources', 'sources.id', '=', 'leads.source_id')
            ->select(
                'users.name',
                'sources.source_name',
                'sources.description',
                'leads.company_name',
                'leads.prospect_first_name',
                'leads.prospect_last_name',
                'leads.designation',
                'leads.linkedin_address',
                'notes.feedback',
                'notes.reminder_for',
                'notes.updated_at as note_updated_at',
                'notes.phone_number'
            );

        // Role-based restrictions (only if a user is authenticated, i.e., web request)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_admin == 1) {
                // Employee role: restricted to their own leads
                $query->where('leads.asign_to', $user->id);
            } elseif ($user->is_admin == 3 || $user->is_admin == 2) {
                // Sub-manager (3) / Manager (2) role: restricted to their managed employee IDs
                $employee_ids = User::where([
                    'user_id' => $user->id,
                    'is_admin' => '1'
                ])->pluck('id');

                $query->whereIn('leads.asign_to', $employee_ids);
            }
            // Super Admin ($user->is_admin == null) has no restrictions
        }

        // Employee filter
        if (!empty($this->emp_id)) {
            $query->where('leads.asign_to', $this->emp_id);
        }

        // Campaign filter
        if (!empty($this->camp_id)) {
            $query->where('notes.source_id', $this->camp_id);
        }

        // Date filter
        if ($date_from_new && $date_to_new) {
            $query->whereBetween('notes.updated_at', [$date_from_new, $date_to_new]);
        }

        // Conversation type filter
        if ($this->filter_by) {
            if ($this->filter_by == 1) {
                $query->whereNull('notes.reminder_for');
            } elseif ($this->filter_by == 2) {
                if (!empty($this->reminder_for_conversation)) {
                    $query->where('notes.reminder_for', $this->reminder_for_conversation);
                } else {
                    $query->whereNotNull('notes.reminder_for');
                }
            }
        }

        // Only conversation filter (fallback for scheduler/legacy)
        if (!empty($this->onlyConversation)) {
            if ($this->onlyConversation === true || $this->onlyConversation === 1 || $this->onlyConversation === '1') {
                $query->whereNotNull('notes.reminder_for');
            } else {
                $query->where('notes.reminder_for', $this->onlyConversation);
            }
        }

        return $query->orderBy('notes.updated_at', 'desc');
    }

    // Map data to the rows of the Excel
    // public function map($lead): array
    // {
    //     return [
    //         $lead->name,
    //         $lead->source_name,
    //         $lead->description,
    //         $lead->company_name,
    //         $lead->prospect_first_name . ' ' . $lead->prospect_last_name,
    //         $lead->designation,
    //         $lead->linkedin_address,
    //         $lead->feedback,
    //         $lead->reminder_for,
    //         date('d/m/Y', strtotime('-1 hour -3 minutes', strtotime($lead->updated_at))),
    //         date('h:i a', strtotime('-1 hour -3 minutes', strtotime($lead->updated_at))),       
    //         $lead->phone_number,
    //     ];
    // }

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
            date('d/m/Y', strtotime($lead->note_updated_at)),
            date('H:i', strtotime($lead->note_updated_at)),
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
            'Employee Name',
            'Campaign Name',
            'Sub-Campaign Name',
            'Organization',
            'Prospect Name',
            'Designation',
            'LinkedIn',
            'Notes',
            'Conversation Type',
            'Comment Date',
            'Comment Time',
            'Note Phone Number'
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
