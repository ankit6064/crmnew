<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\User;
use App\Models\Lead;
use App\Models\Note;
use Auth;
use Yajra\DataTables\DataTables;

class LogsController extends Controller
{
    //
    public function employeelogs(Request $request)
    {
        $employee = User::select('id', 'first_name', 'last_name')->where('user_id', Auth::id())->orderby('first_name')->get();
        $sources = Source::select('id', 'source_name', 'description')->where('assign_to_manager', Auth::id())->orderby('source_name')->get();
        return view('logs.employeelogs', compact('employee', 'sources'));
    }

    public function filteremployeelogs(Request $request)
    {
        // Optimize: Get employee IDs efficiently
        $employeeids = !empty($request->employeeid)
            ? [$request->employeeid]
            : User::where('user_id', Auth::id())->pluck('id')->toArray();
    
        // Early return if no employees found
        if (empty($employeeids)) {
            return DataTables::of(collect())->make(true);
        }
    
        // Optimize: Select only needed columns and use indexes
        $logsQuery = Logs::select(
            'logs.id',
            'logs.user_id',
            'logs.type',
            'logs.note_id',
            'logs.reference_id',
            'logs.created_at',
            'users.first_name',
            'users.last_name',
            'leads.prospect_first_name',
            'leads.prospect_last_name',
            'sources.source_name',
            'sources.description as source_description'
        )
            ->leftJoin('users', 'users.id', '=', 'logs.user_id')
            ->leftJoin('leads', 'leads.id', '=', 'logs.reference_id')
            ->leftJoin('sources', 'sources.id', '=', 'leads.source_id')
            ->whereIn('logs.user_id', $employeeids);
    
        // Optimize: Apply date filter with proper indexing
        if (!empty($request->date)) {
            $separator = strpos($request->date, ' to ') !== false ? ' to ' : ' - ';
            [$start, $end] = array_map('trim', explode($separator, $request->date));
            $startDate = $start . ' 00:00:00';
            $endDate = $end . ' 23:59:59';
            
            // Use whereBetween for better index usage
            $logsQuery->whereBetween('logs.created_at', [$startDate, $endDate]);
        }
    
        // Apply other filters
        if (!empty($request->sourceid)) {
            $logsQuery->where('sources.id', $request->sourceid);
        }
        
        if (!empty($request->type)) {
            $logsQuery->where('logs.type', $request->type);
        }
    
        // Optimize: Batch load notes data to avoid N+1 queries
        $logsQuery->addSelect([
            'note_reminder_for' => Note::select('reminder_for')
                ->whereColumn('notes.id', 'logs.note_id')
                ->limit(1)
        ]);
    
        return DataTables::of($logsQuery)
            ->addColumn('employeename', function ($log) {
                return $log->first_name . ' ' . $log->last_name;
            })
            ->addColumn('campaign_name', function ($log) {
                return $log->source_name && $log->source_description
                    ? $log->source_name . ' - ' . $log->source_description
                    : '';
            })
            ->addColumn('lead_name', function ($log) {
                return $log->prospect_first_name && $log->prospect_last_name
                    ? $log->prospect_first_name . ' ' . $log->prospect_last_name
                    : '';
            })
            ->addColumn('description', function ($log) {
                // Use pre-fetched note data when possible
                $type = (int) $log->type;
                
                $desc = match ($type) {
                    1 => $log->note_reminder_for ?? '',
                    2 => 'Lead status updated',
                    3 => 'LHS created',
                    4 => 'MOM report generated',
                    5 => 'A new lead is added',
                    6 => 'LHS report updated',
                    17 => 'LHS Sent',
                    18 => 'LHS Reminder Sent',
                    19 => 'Lead Confirmed',
                    default => '',
                };
                return trim($desc) !== '' ? $desc : 'N/A';
            })
            ->addColumn('type', fn($log) => $this->getTypeText($log->type))
            ->editColumn('created_at', fn($log) => $log->created_at?->format('d-m-Y H:i') ?? '')
            ->filterColumn('employeename', function ($query, $keyword) {
                $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('campaign_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(sources.source_name, ' - ', sources.description) LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('employeename', function ($query, $direction) {
                $query->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) {$direction}");
            })
            ->orderColumn('campaign_name', function ($query, $direction) {
                $query->orderBy('sources.source_name', $direction)->orderBy('sources.description', $direction);
            })
            ->orderColumn('created_at', function ($query, $direction) {
                $query->orderBy('logs.created_at', $direction);
            })
            ->rawColumns(['description'])
            ->make(true);
    }


    private function getTypeText($type)
    {
        return match ((int) $type) {
            1 => 'Note Added',
            2 => 'Lead Status',
            3 => 'LHS Generated',
            4 => 'MOM Generated',
            5 => 'New Lead',
            6 => 'LHS Updated',
            17 => 'LHS Sent',
            18 => 'LHS Reminder Sent',
            19 => 'Lead Confirmed',
            20 => 'Meeting Status Updated',
            default => 'Unknown',
        };
    }

    public function managerlogs(Request $request)
    {


        if ($request->ajax()) {
            // Fetch leads with relationships and filters
            $query = Logs::where('logs.user_id', Auth::id())
                ->select('logs.*', 'sources.source_name', 'sources.description as source_description')
                ->leftJoin('sources', 'sources.id', 'logs.source_id');
            if (isset($request->sourceid) && !empty($request->sourceid)) {
                $query->where('logs.source_id', $request->sourceid);
            }
            if (!empty($request->date)) {
                $separator = strpos($request->date, ' to ') !== false ? ' to ' : ' - ';
                [$start, $end] = array_map('trim', explode($separator, $request->date));
                $query->whereBetween('logs.created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
            }
            if (isset($request->type) && !empty($request->type)) {
                $query->where('logs.type', $request->type);
            }
            $data = $query->orderBy('logs.created_at', 'desc')->get();

            return DataTables::of($data)
                ->addColumn('campaign', function ($row) {
                    if (isset($row->source_name) && !empty($row->source_name)) {
                        return $row->source_name . '-' . $row->source_description;
                    } else {
                        return '--';
                    }

                })->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d-m-y H:i');
                })
                ->editColumn('description', function ($row) {
                    $description = trim($row->description ?? '');
                    if ($description === '') {
                        return 'N/A';
                    }
                    $full = htmlspecialchars($description);
                    $short = strlen($description) > 100
                        ? substr($description, 0, 100) . '...'
                        : $description;

                    return '<span title="' . $full . '">' . e($short) . '</span>';
                })

                ->editColumn('type', function ($row) {
                    $types = [
                        1 => 'Note Added',
                        2 => 'Lead Status Updated',
                        3 => 'LHS Created',
                        4 => 'MOM Report Generated',
                        5 => 'New Lead Added',
                        6 => 'LHS Updated',
                        7 => 'Account Added',
                        8 => 'Manage Login',
                        9 => 'Account Active/Deactive',
                        10 => 'Assigned Submanager',
                        11 => 'Account Deleted',
                        12 => 'Company Transferred',
                        13 => 'Campaign Active/Deactive',
                        14 => 'New Campaign Added',
                        15 => 'Lead Approved/Disapproved',
                        16 => 'Employee Assigned',
                        17 => 'LHS Sent',
                        18 => 'LHS Reminder Sent',
                        19 => 'Lead Confirmed',
                        20 => 'Meeting Status Updated'
                    ];

                    return $types[$row->type] ?? 'N/A';
                })
                ->rawColumns(['description'])

                ->make(true);
        }
        $sources = Source::select('id', 'source_name', 'description')->where('assign_to_manager', Auth::id())->orderby('source_name')->get();

        return view('logs.Managerlogs', compact('sources'));
    }


    public function leadslogs(Request $request)
    {
        $employee = User::select('id', 'first_name', 'last_name')->where('user_id', Auth::id())->orderby('first_name')->get();
        $sources = Source::select('id', 'source_name', 'description')->where('assign_to_manager', Auth::id())->orderby('source_name')->get();
        return view('logs.leadslogs', compact('employee', 'sources'));
    }

    public function filterleadslogs(Request $request)
    {
        $employeeids = !empty($request->employeeid)
            ? [$request->employeeid]
            : User::where('user_id', Auth::id())->pluck('id');

        // Join users, leads, and sources to make sorting work
        $logsQuery = Logs::select(
            'logs.*',
            'leads.id as lead_id',
            'leads.prospect_first_name',
            'leads.prospect_last_name',
            'sources.source_name',
            'leads.company_name',
            'sources.description as source_description'
        )
            ->Join('leads', 'leads.id', '=', 'logs.reference_id')
            ->Join('sources', 'sources.id', '=', 'leads.source_id')
            ->groupBy('logs.reference_id')
            ->where(function ($query) use ($employeeids) {
                $query->whereIn('logs.user_id', $employeeids)
                    ->orWhere('logs.user_id', Auth::id());
            });
        if (!empty($request->date)) {
            $separator = strpos($request->date, ' to ') !== false ? ' to ' : ' - ';
            [$start, $end] = array_map('trim', explode($separator, $request->date));
            $logsQuery->whereBetween('logs.created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        }


        if (!empty($request->sourceid)) {
            $logsQuery->where('sources.id', $request->sourceid);
        }
        if (!empty($request->type)) {
            $logsQuery->where('logs.type', $request->type);
        }

        return DataTables::of($logsQuery)
            ->addColumn('campaign_name', function ($log) {
                return $log->source_name && $log->source_description
                    ? $log->source_name . ' - ' . $log->source_description
                    : '';
            })
            ->addColumn('lead_name', function ($log) {
                return $log->prospect_first_name && $log->prospect_last_name
                    ? $log->prospect_first_name . ' ' . $log->prospect_last_name
                    : '';
            })
            ->addColumn('viewlogs', function ($log) {
                $button = '<a href="' . route('viewleadlogs', ['id' => $log->lead_id]) . '" title="view lead logs"><i class="fa fa-eye label-new" aria-hidden="true" ></i></a>';
                return $button;


            })
            ->rawColumns(['viewlogs'])
            ->make(true);
    }


    public function viewleadlogs(Request $request, $id)
    {
        $employee = User::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $sources = Source::select('id', 'source_name', 'description')->orderBy('source_name')->get();
        $leadDetails = Lead::with(['source', 'user', 'leadManager'])->where('id', $id)->first();
        if (!$leadDetails) {
            abort(404, 'Lead not found');
        }
        return view('logs.viewleadlogs', compact('employee', 'sources', 'id', 'leadDetails'));
    }


    public function viewleadlogstable(Request $request)
    {
        $logsQuery = Logs::select(
            'logs.*',
            'users.first_name',
            'users.last_name',
            'leads.prospect_first_name',
            'leads.prospect_last_name',
            'leads.company_name',
            'sources.source_name',
            'sources.description as source_description'
        )
            ->leftJoin('users', 'users.id', '=', 'logs.user_id')
            ->leftJoin('leads', 'leads.id', '=', 'logs.reference_id')
            ->leftJoin('sources', 'sources.id', '=', 'leads.source_id')
            ->where('logs.reference_id', $request->lead_id);

        if (!empty($request->date)) {
            $separator = strpos($request->date, ' to ') !== false ? ' to ' : ' - ';
            [$start, $end] = array_map('trim', explode($separator, $request->date));
            $logsQuery->whereBetween('logs.created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        }

        if (!empty($request->type)) {
            $logsQuery->where('logs.type', $request->type);
        }

        if (!empty($request->employeeid)) {
            $logsQuery->where('logs.user_id', $request->employeeid);
        }

        return DataTables::of($logsQuery)
            ->addColumn('employeename', function ($log) {
                $name = trim(($log->first_name ?? '') . ' ' . ($log->last_name ?? ''));
                return !empty($name) ? htmlspecialchars($name) : 'System';
            })
            ->addColumn('type_badge', function ($log) {
                $type = (int) $log->type;
                switch ($type) {
                    case 1:
                        return '<span class="badge" style="background:#17a2b8; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-sticky-note me-1"></i> Note Added</span>';
                    case 2:
                        return '<span class="badge" style="background:#ffc107; color:#000; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-sync-alt me-1"></i> Status Updated</span>';
                    case 3:
                        return '<span class="badge" style="background:#007bff; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-file-alt me-1"></i> LHS Created</span>';
                    case 4:
                        return '<span class="badge" style="background:#6c757d; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-file-signature me-1"></i> MOM Report</span>';
                    case 5:
                        return '<span class="badge" style="background:#28a745; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-plus-circle me-1"></i> Lead Added</span>';
                    case 6:
                        return '<span class="badge" style="background:#007bff; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-edit me-1"></i> LHS Updated</span>';
                    case 12:
                        return '<span class="badge" style="background:#6f42c1; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-exchange-alt me-1"></i> Transferred</span>';
                    case 15:
                        return '<span class="badge" style="background:#fd7e14; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-check-double me-1"></i> Approval Status</span>';
                    case 16:
                        return '<span class="badge" style="background:#20c997; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-user-check me-1"></i> Assigned</span>';
                    case 17:
                        return '<span class="badge" style="background:#007bff; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-paper-plane me-1"></i> LHS Sent</span>';
                    case 18:
                        return '<span class="badge" style="background:#ffc107; color:#000; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-bell me-1"></i> LHS Reminder</span>';
                    case 19:
                        return '<span class="badge" style="background:#28a745; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-check-circle me-1"></i> Confirmed</span>';
                    case 20:
                        return '<span class="badge" style="background:#17a2b8; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-calendar-check me-1"></i> Meeting Status</span>';
                    case 21:
                        return '<span class="badge" style="background:#e83e8c; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-undo me-1"></i> Reverted to Fresh</span>';
                    default:
                        return '<span class="badge" style="background:#6c757d; color:#fff; padding:5px 10px; border-radius:4px; font-weight:600;"><i class="fas fa-history me-1"></i> Activity</span>';
                }
            })
            ->editColumn('description', function ($log) {
                if (!empty($log->description)) {
                    return htmlspecialchars($log->description);
                }
                if ($log->type == 1 && !empty($log->note_id)) {
                    $note = Note::find($log->note_id);
                    if ($note) {
                        return htmlspecialchars('Note Added: ' . ($note->feedback ?? 'No feedback text'));
                    }
                }
                return match ((int) $log->type) {
                    1 => 'Note added to lead',
                    2 => 'Lead status updated',
                    3 => 'LHS report created',
                    4 => 'MOM report generated',
                    5 => 'New lead added to system',
                    6 => 'LHS report updated',
                    12 => 'Company/Lead transferred',
                    15 => 'Lead approval status updated',
                    16 => 'Lead assigned to user',
                    17 => 'LHS report sent',
                    18 => 'LHS reminder sent',
                    19 => 'Lead confirmed',
                    20 => 'Meeting status updated',
                    21 => 'Lead automatically reverted to fresh lead',
                    default => 'Activity logged for lead',
                };
            })
            ->editColumn('created_at', fn($log) => $log->created_at ? $log->created_at->format('d M, Y h:i A') : '')
            ->rawColumns(['type_badge', 'description'])
            ->make(true);
    }





}
