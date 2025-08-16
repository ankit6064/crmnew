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
        $counts = Lead::select([
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('SUM(CASE WHEN status = ' . LEAD_STATUS_PENDING . ' THEN 1 ELSE 0 END) as total_pending_leads'),
            DB::raw('SUM(CASE WHEN status = ' . LEAD_STATUS_FAILED . ' THEN 1 ELSE 0 END) as total_failed_leads'),
            DB::raw('SUM(CASE WHEN status = ' . LEAD_STATUS_CLOSED . ' THEN 1 ELSE 0 END) as total_closed_leads'),
            DB::raw('SUM(CASE WHEN status = ' . LEAD_STATUS_INPROGRESS . ' THEN 1 ELSE 0 END) as total_inprogress_leads'),
            DB::raw('SUM(CASE WHEN status = ' . LEAD_STATUS_COMPLETED . ' THEN 1 ELSE 0 END) as total_completed_leads'),

        ])->first();

        $totalLeads = $counts->total_leads;
        $totalPendingLeads = $counts->total_pending_leads;
        $totalFailedLeads = $counts->total_failed_leads;
        $totalClosedLeads = $counts->total_closed_leads;
        $totalInprogressLeads = $counts->total_inprogress_leads;
        $total_completed_leads = $counts->total_completed_leads;

        // Return view with lead counts
        return view('dashboard', [
            'totalLeads' => $totalLeads,
            'totalPendingLeads' => $totalPendingLeads,
            'totalClosedLeads' => $totalClosedLeads,
            'totalFailedLeads' => $totalFailedLeads,
            'totalInProgressLeads' => $totalInprogressLeads,
            'total_completed_leads' => $total_completed_leads
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

        // Retrieve all counts in one query
        $leadCounts = Lead::selectRaw('
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
            'totalClosedLeads' => $leadCounts->totalClosedLeads,
            'totalFailedLeads' => $leadCounts->totalFailedLeads,
            'totalInprogressLeads' => $leadCounts->totalInprogressLeads,
            'totalCompletedLeads' => $leadCounts->totalCompletedLeads,
            'todayReminders' => $todayReminders,
        ]);
    }

    public function managerdashboard(Request $request)
    {
        $submangercount = User::where('is_admin', 3)->count();
        $employeecount = User::where('is_admin', 1)->count();
        $campaigncount = Source::where('assign_to_manager', Auth::id())->count();
        $totalleads = Lead::where('asign_to_manager', Auth::id())->count();
        $totallhscount = Lead::join('lhs_report', 'leads.id', 'lhs_report.lead_id')->where('leads.asign_to_manager', Auth::id())->count();
        $totalmomcount = Lead::join('mom_report', 'leads.id', 'mom_report.lead_id')->where('leads.asign_to_manager', Auth::id())->count();

        $employee = User::select('id', 'first_name', 'last_name')->where('user_id', Auth::id())->orderby('first_name')->get();
        $sources = Source::select('id', 'source_name', 'description')->where('assign_to_manager', Auth::id())->orderby('source_name')->get();
        return view('managerdashboard', compact('submangercount', 'employeecount', 'campaigncount', 'totalleads', 'totallhscount', 'totalmomcount','employee','sources'));
    }

    public function getmanagergraph(Request $request)
    {      
        if (!empty($request->date)) {
            [$start, $end] = array_map('trim', explode(' - ', $request->date));
            $start = Carbon::parse($start)->setTime(6, 41);  // set start date at 06:41
            $end   = Carbon::parse($end)->addDay()->setTime(6, 40); // set next day at 06:40
        } else {
            $start = Carbon::today()->setTime(6, 41);  // today 06:41
            $end   = Carbon::tomorrow()->setTime(6, 40); // tomorrow 06:40
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

        // Fetch only necessary user fields
        $lastLogin = User::where('id', $userId)->value('last_login') ?? now();

        // Optimize Query with Indexing & Preloading
        $query = Lead::where('asign_to', $userId)
            ->where('status', '!=', '2')
            ->whereHas('source', fn($q) => $q->where('is_active', '1'))
            ->select('source_id', DB::raw('COUNT(*) as totalLeads'))
            ->groupBy('source_id');

        // Fetch all related sources in a single query
        $sourceData = Source::whereIn('id', $query->pluck('source_id'))
            ->select('id', 'source_name', 'description')
            ->get()
            ->keyBy('id'); // Use keyBy for quick lookup

        // Fetch all note counts in a single query
        $noteCounts = Note::whereIn('source_id', $query->pluck('source_id'))
            ->where('created_at', '>=', $lastLogin)
            ->groupBy('source_id')
            ->select('source_id', DB::raw('COUNT(*) as notes_count'))
            ->pluck('notes_count', 'source_id'); // Key by source_id for fast retrieval

        return datatables()->of($query)
            ->addColumn('campaign_name', function ($data) use ($sourceData) {
                $source = $sourceData[$data->source_id] ?? null;
                if ($source) {
                    return '<a href="' . url('campaign/camp_assign_emp/' . $data->source_id) . '" 
                            class="set_camp_id" target="_blank">
                            <span class="label" data-toggle="tooltip" data-placement="top" 
                                title="View Campaign" 
                                style="color:#000;font-size: 15px;">'
                        . $source->source_name . '</span></a>';
                }
                return '--';
            })
            ->addColumn('description', fn($data) => $sourceData[$data->source_id]->description ?? '--')
            ->addColumn('totalLeads', fn($data) => $data->totalLeads)
            ->addColumn('last_login', fn() => date("d-m-Y H:i:s", strtotime($lastLogin)))
            ->addColumn('notes_count', fn($data) => $noteCounts[$data->source_id] ?? 0)
            ->rawColumns(['campaign_name']) // Allow HTML in campaign_name
            ->make(true);
    }
}
