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
        $employeeids = !empty($request->employeeid)
            ? [$request->employeeid]
            : User::where('user_id', Auth::id())->pluck('id');

        // Join users, leads, and sources to make sorting work
        $logsQuery = Logs::select(
            'logs.*',
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

        if (!empty($request->date)) {
            [$start, $end] = array_map('trim', explode(' - ', $request->date));
            $logsQuery->whereBetween('logs.created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        }


        if (!empty($request->sourceid)) {
            $logsQuery->where('sources.id', $request->sourceid);
        }
        if (!empty($request->type)) {
            $logsQuery->where('logs.type', $request->type);
        }

        return DataTables::of($logsQuery)
            ->addColumn('employeename', fn($log) => $log->first_name . ' ' . $log->last_name)
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
                return match ((int) $log->type) {
                    1 => Note::where('id', $log->note_id)->value('reminder_for') ?? '',
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
            })
            ->addColumn('type', fn($log) => $this->getTypeText($log->type))
            ->editColumn('created_at', fn($log) => $log->created_at ? $log->created_at->format('d-m-Y H:i') : '')
            ->filterColumn('employeename', function ($query, $keyword) {
                $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('campaign_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(sources.source_name, ' ', sources.description) LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('employeename', function ($query, $direction) {
                $query->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) {$direction}");
            })
            ->orderColumn('campaign_name', function ($query, $direction) {
                $query->orderBy('sources.source_name', $direction);
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
                [$start, $end] = array_map('trim', explode(' - ', $request->date));
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
                    $full = htmlspecialchars($row->description);
                    $short = strlen($row->description) > 100
                        ? substr($row->description, 0, 100) . '...'
                        : $row->description;

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
                        19 => 'Lead Confirmed'
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
            [$start, $end] = array_map('trim', explode(' - ', $request->date));
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
        $employee = User::select('id', 'first_name', 'last_name')->where('user_id', Auth::id())->orderby('first_name')->get();
        $sources = Source::select('id', 'source_name', 'description')->where('assign_to_manager', Auth::id())->orderby('source_name')->get();
        $leadDetails = Lead::where('id', $id)->first();
        return view('logs.viewleadlogs', compact('employee', 'sources', 'id', 'leadDetails'));
    }


    public function viewleadlogstable(Request $request)
    {


        // Join users, leads, and sources to make sorting work
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
            [$start, $end] = array_map('trim', explode(' - ', $request->date));
            $logsQuery->whereBetween('logs.created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        }


        if (!empty($request->sourceid)) {
            $logsQuery->where('sources.id', $request->sourceid);
        }
        if (!empty($request->type)) {
            $logsQuery->where('logs.type', $request->type);
        }
        if (!empty($request->employeeid)) {
            $logsQuery->where('logs.user_id', $request->employeeid);
        }

        return DataTables::of($logsQuery)
            ->addColumn('employeename', fn($log) => $log->first_name . ' ' . $log->last_name)
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
            ->editColumn('description', function ($log) {
                return $log->description . ' By ' . $log->first_name . ' ' . $log->last_name;
            })
            ->addColumn('type', function ($log) {
                return match ((int) $log->type) {
                    1 => Note::where('id', $log->note_id)->value('reminder_for') ?? '',
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
                    default => '',
                };
            })
            ->editColumn('created_at', fn($log) => $log->created_at ? $log->created_at->format('d-m-Y H:i') : '')
            ->rawColumns(['description'])
            ->make(true);
    }





}
