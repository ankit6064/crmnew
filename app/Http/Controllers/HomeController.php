<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;
use App\Models\Source;
use App\Models\Note;
use App\Models\User;
use App\Models\CallbackLeads;
use yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;


class HomeController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalLeads = Lead::join('sources', 'sources.id', 'leads.source_id')->count();
        $totalsubmanagers = User::where('is_admin', SUBMANAGER)->count();
        $totalemployees = User::where('is_admin', EMPLOYEE_ROLE)->count();
        $totalmanagers = User::where('is_admin', MANAGER)->count();
        $totalcampaigns = Source::count();
        $totallhscount = Lead::join('lhs_report', 'leads.id', 'lhs_report.lead_id')->count();
        $totalmomcount = Lead::join('mom_report', 'leads.id', 'mom_report.lead_id')->count();

        // Return view with lead counts
        return view('dashboard', [
            'totalLeads' => $totalLeads,
            'totalsubmanagers' => $totalsubmanagers,
            'totalemployees' => $totalemployees,
            'totalmanagers' => $totalmanagers,
            'totalcampaigns' => $totalcampaigns,
            'totallhscount' => $totallhscount,
            'totalmomcount' => $totalmomcount
        ]);
    }


    public function home(Request $request)
    {
        return view('home');
    }
    public function home_datatable(Request $request)
    {
        if (request()->ajax()) {
            $user_id = auth()->user()->id;

            $data = Relation::select([
                'users.id',
                'users.name',
                'relations.assign_to_cam',
                'relations.lead_assigned',
                'users.last_login',
                'sources.source_name',
                'sources.description'
            ])
                ->join('users', 'relations.assign_to_employee', '=', 'users.id')
                ->join('sources', 'sources.id', '=', 'relations.assign_to_cam')
                ->where('relations.assign_to_manager', '=', $user_id)
                ->whereNull('users.deleted_at')
                ->whereNotNull(['relations.assign_to_cam', 'relations.assign_to_employee', 'relations.assign_to_manager']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->filter(function ($query) {
                    // Handle global search
                    if ($search = request('search')['value']) {
                        $query->where(function ($q) use ($search) {
                            $q->where('users.name', 'LIKE', "%$search%")
                                ->orWhere('relations.lead_assigned', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addColumn('last_login_new', fn($row) => isset($row->last_login) ? date("d-m-Y H:i:s", strtotime($row->last_login)) : "--")
                ->addColumn('notes_count', function ($row) {
                    return Note::where('source_id', $row->assign_to_cam)
                        ->where('created_at', '>=', $row->last_login) // Future date optimization removed
                        ->count();
                })
                ->toJson();
        }
    }



    public function employeedashboard()
    {
        $userId = auth()->user()->id;
        $today = Carbon::today();
        $totalCampaign = Source::whereHas('leads', function ($q) use ($userId) {
            $q->where('asign_to', $userId)
                ->where('status', '!=', '2');
        })->count();
        // Retrieve all counts in one query
        $leadCounts = Lead::selectRaw('
            COUNT(*) as totalLeads,
            SUM(CASE WHEN status = "1" THEN 1 ELSE 0 END) as freshleads,
            SUM(CASE WHEN status = "3" THEN 1 ELSE 0 END) as totalClosedLeads,
            SUM(CASE WHEN status = "2" THEN 1 ELSE 0 END) as totalFailedLeads,
            SUM(CASE WHEN status = "4" THEN 1 ELSE 0 END) as totalInprogressLeads,
            SUM(CASE WHEN status = "5" THEN 1 ELSE 0 END) as totalCompletedLeads
            ')
            ->where('asign_to', $userId)
            ->whereHas('source', function ($query) {
                $query->where('is_active', 1);
            })
            ->first();

        // $leadsd = Lead::where('status','5')->get();
        // dd($leadsd);

        // Retrieve today's reminders
        $todayReminders = Note::where('user_id', $userId)
            ->whereDate('reminder_date', $today)
            ->count();

        return view('employeedashboard', [
            'totalLeads' => $leadCounts->totalLeads,
            'totalFreshLeads' => $leadCounts->freshleads,
            'totalClosedLeads' => $leadCounts->totalClosedLeads,
            'totalFailedLeads' => $leadCounts->totalFailedLeads,
            'totalInprogressLeads' => $leadCounts->totalInprogressLeads,
            'totalCompletedLeads' => $leadCounts->totalCompletedLeads,
            'todayReminders' => $todayReminders,
            'totalCampaign' => $totalCampaign
        ]);
    }

    public function managerdashboard(Request $request)
    {
        $submangercount = User::where('is_admin', 3)->where('user_id', Auth::id())->count();
        $employeecount = User::whereIn('is_admin', [1, 3])->where('user_id', Auth::id())->count();
        $campaigncount = Source::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhere('assign_to_manager', auth()->user()->id);
        })
            ->count();
        $totalleads = Lead::where('asign_to_manager', Auth::id())->count();
        $totallhscount = Lead::where('asign_to_manager', Auth::id())->whereNotNull('invitation_date')->count();
        $totalmomcount = Lead::where('asign_to_manager', Auth::id())->where('meeting_status', 'Done')->count();
        $totalfailedcount = Lead::where('asign_to_manager', Auth::id())->where('meeting_status', 'Failed')->count();

        $employee = User::select('id', 'first_name', 'last_name')->where('user_id', Auth::id())->orderby('first_name')->get();
        $sources = Source::select('id', 'source_name', 'description')->where('assign_to_manager', Auth::id())->orderby('source_name')->get();


        $currentDate = now()->format('Y-m-d'); // 2025-05-14
        $currentTime = now()->format('H:i'); // e.g., 14:27 (use 'H:i' for 24-hour format to avoid AM/PM issues)

        $employeeids = User::where('user_id', auth()->user()->id)->pluck('id');

        $callbackleads = CallbackLeads::query()
            ->join('leads', 'leads.id', '=', 'callback_leads.lead_id')
            ->join('sources', 'sources.id', '=', 'leads.source_id')
            ->join('users', 'users.id', '=', 'callback_leads.employee_id')
            ->select(
                'callback_leads.*',
                DB::raw("CONCAT(leads.prospect_first_name, ' ', leads.prospect_last_name) as lead_name"),
                'sources.source_name',
                'sources.description',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as employee_name")
            )
            ->whereIn('callback_leads.employee_id', $employeeids)
            ->whereDate('callback_leads.callback_date', $currentDate)->count();

        return view('managerdashboard', compact('submangercount', 'employeecount', 'campaigncount', 'totalleads', 'totallhscount', 'totalmomcount', 'totalfailedcount', 'employee', 'sources', 'callbackleads'));
    }

    public function getmanagergraph(Request $request)
    {
        if (!empty($request->date)) {
            [$start, $end] = array_map('trim', explode(' - ', $request->date));
            $start = Carbon::parse($start)->setTime(6, 41);  // set start date at 06:41
            $end = Carbon::parse($end)->addDay()->setTime(6, 40); // set next day at 06:40
        } else {
            $start = Carbon::today()->setTime(6, 41);  // today 06:41
            $end = Carbon::tomorrow()->setTime(6, 40); // tomorrow 06:40
        }

        $campaignId = $request->input('campaign_id');
        $employeeId = $request->input('employee_id');

        // Leads
        $leads = Lead::where('asign_to_manager', Auth::id())
            ->where('status', '!=', 3)
            ->when($campaignId, fn($q) => $q->where('source_id', $campaignId))
            ->when($employeeId, fn($q) => $q->where('user_id', $employeeId))
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Notes
        $notes = Lead::join('notes', 'notes.lead_id', 'leads.id')
            ->where('leads.asign_to_manager', Auth::id())
            ->when($campaignId, fn($q) => $q->where('leads.source_id', $campaignId))
            ->when($employeeId, fn($q) => $q->where('leads.user_id', $employeeId))
            ->whereBetween('notes.created_at', [$start, $end])
            ->count();

        // Closed Leads
        $closed = Lead::where('asign_to_manager', Auth::id())
            ->where('status', 3)
            ->when($campaignId, fn($q) => $q->where('source_id', $campaignId))
            ->when($employeeId, fn($q) => $q->where('user_id', $employeeId))
            ->whereBetween('closed_on', [$start, $end])
            ->count();

        // Completed Leads
        $completed = Lead::join('mom_report', 'leads.id', 'mom_report.lead_id')
            ->where('leads.asign_to_manager', Auth::id())
            ->when($campaignId, fn($q) => $q->where('leads.source_id', $campaignId))
            ->when($employeeId, fn($q) => $q->where('leads.user_id', $employeeId))
            ->whereBetween('mom_report.created_at', [$start, $end])
            ->count();


        return response()->json([
            'labels' => ['Leads', 'Notes', 'Closed', 'Completed'],
            'values' => [$leads, $notes, $closed, $completed]
        ]);

    }

    public function geEmployeeDashboardData(Request $request)
    {
        $userId = auth()->user()->id;

        $statusFilter = $request->get('status_filter', 'total');
        $searchValue = $request->get('search')['value'] ?? null;

        $lastLogin = User::where('id', $userId)->value('last_login') ?? now();

        $query = Lead::where('asign_to', $userId)
            ->whereHas('source', function ($q) use ($statusFilter, $searchValue) {

                // Status filter
                if ($statusFilter == 'active') {
                    $q->where('is_active', '1');
                } elseif ($statusFilter == 'inactive') {
                    $q->where('is_active', '2');
                } else {
                    $q->whereIn('is_active', ['1', '2']);
                }

                // Search filter
                if ($searchValue) {
                    $q->where(function ($s) use ($searchValue) {
                        $s->where('source_name', 'LIKE', "%{$searchValue}%")
                            ->orWhere('description', 'LIKE', "%{$searchValue}%");
                    });
                }
            })
            ->select('source_id', DB::raw('COUNT(*) as totalLeads'))
            ->groupBy('source_id');

        $sourceIds = $query->pluck('source_id');

        // Fetch source info
        $sourceData = Source::whereIn('id', $sourceIds)
            ->select('id', 'source_name', 'description')
            ->get()
            ->keyBy('id');

        // Fetch note counts
        $noteCounts = Note::whereIn('source_id', $sourceIds)
            ->where('created_at', '>=', $lastLogin)
            ->groupBy('source_id')
            ->select('source_id', DB::raw('COUNT(*) as notes_count'))
            ->pluck('notes_count', 'source_id');

        return datatables()->of($query)

            ->addColumn('campaign_name', function ($data) use ($sourceData) {

                $source = $sourceData[$data->source_id] ?? null;

                if ($source) {
                    return '
                            <span class="label" data-toggle="tooltip"
                            title="View Campaign"
                            style="color:#000;font-size:15px;">'
                        . $source->source_name . '</span>';
                }

                return '--';
            })

            ->addColumn('description', fn($data) => $sourceData[$data->source_id]->description ?? '--')

            ->addColumn('totalLeads', fn($data) => $data->totalLeads)

            ->addColumn('last_login', fn() => date("d-m-Y H:i:s", strtotime($lastLogin)))

            ->addColumn('notes_count', fn($data) => $noteCounts[$data->source_id] ?? 0)

            ->addColumn('action', function ($data) use ($sourceData) {

                $html = ' <a href="' . url('campaign/camp_assign_emp/' . $data->source_id) . '">
                <span class="label viewleads" data-tippy-content="View Leads" style="color:#000;font-size:15px;">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </a>';
                return $html;
            })

            ->rawColumns(['campaign_name', 'action'])

            ->make(true);
    }
}
