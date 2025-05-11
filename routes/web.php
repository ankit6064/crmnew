<?php

use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SourcesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\LeadClosedCsv;
use App\Http\Controllers\LeadClosedController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
Route::fallback(function () {
    return redirect()->route('login')->with('error', 'Invalid route');
});

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/home', [HomeController::class, 'home'])->middleware(['auth', 'verified'])->name('home');
Route::get('/home_datatable', [HomeController::class, 'home_datatable'])->middleware(['auth', 'verified'])->name('home_datatable');
Route::get('/employeedashboard', [HomeController::class, 'employeedashboard'])->middleware(['auth', 'verified'])->name('employeedashboard');
Route::get('/geEmployeeDashboardData', [HomeController::class, 'geEmployeeDashboardData'])->middleware(['auth', 'verified'])->name('geEmployeeDashboardData');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('lead/export/{id}/pdf_down', [LeadClosedController::class,'generateSinglePDF'])->name('generateSinglePDF');
Route::get('download-mom-report/{id}', [SourcesController::class,'downloadmomreport'])->name('download-mom-report');

Route::prefix('sources')->group(function () {
    Route::get('/', [SourcesController::class, 'index'])->name('sources.index');
    Route::get('data', [SourcesController::class, 'getSources'])->name('sources.data');
    Route::post('update-status', [SourcesController::class, 'updateStatus'])->name('sources.updateStatus');
    Route::post('leads/assign-to', [SourcesController::class, 'getTotalLeadsAssignTo'])->name('sources.leads.assign_to');
    Route::get('create', [SourcesController::class, 'create'])->name('sources.create');
    Route::post('create', [SourcesController::class, 'store'])->name('sources.store');

    Route::get('getMangerSource', [SourcesController::class, 'getMangerSource'])->name('sources.getMangerSource');
    Route::get('leadscount', [SourcesController::class, 'leadscount'])->name('sources.leadscount');

    
    Route::post('updateAmount', [SourcesController::class, 'updateAmount'])->name('updateAmount');
    Route::get('campaigns_list_ajax_pagination', [SourcesController::class, 'campaignsAjaxPagination'])->name('campaigns_list_ajax_pagination');
    Route::post('getSourceAssignedUsers', [SourcesController::class, 'getSourceAssignedUsers'])->name('assigned.users');
    Route::post('assign-lead', [SourcesController::class, 'assignLeadsToUser'])->name('assign.lead');

    Route::get('getSourcesData', [SourcesController::class, 'getSourcesData'])->name('getSourcesData');
    Route::get('manager-users', [SourcesController::class,'usersByManager']);
    Route::get('source-edit/{id}', [SourcesController::class,'sourceEdit'])->name('source-edit');
    Route::patch('update/{id}', [SourcesController::class,'update'])->name('sources.update');
    Route::post('statusUpdate', [SourcesController::class,'statusUpdate'])->name('statusUpdate');

    Route::get('delete/{id}', [SourcesController::class,'delete'])->name('delete');
    Route::post('assignManager', [SourcesController::class,'assignManager'])->name('assignManager');

    

});
Route::prefix('manager')->group(function () {
    // Index route (Manager listing)
    Route::get('/', [ManagerController::class, 'index'])->name('manager.index');
    Route::get('data', [ManagerController::class, 'getManagers'])->name('manager.data');
    // Route to show the manager creation form
    Route::get('create', [ManagerController::class, 'create'])->name('manager.create');
    // Route to save the manager (POST request)
    Route::post('store', [ManagerController::class, 'store'])->name('manager.store');
    Route::get('{manager_id}/edit', [ManagerController::class, 'edit'])->name('manager.edit');
    Route::put('{manager_id}', [ManagerController::class, 'update'])->name('manager.update');
    Route::put('{manager}', [ManagerController::class, 'update'])->name('managers.update');
    Route::post('{manager_id}', [ManagerController::class, 'destroy'])->name('manager.destroy');
    Route::get('employee/{manager_id}', [ManagerController::class, 'managerEmloyees'])->name('manager.employees');
    Route::get('employee/{manager_id}/data', [ManagerController::class, 'getManagerEmloyees'])->name('manager.employees.data');
});
Route::post('storemanageremployeedetail', [EmployeeController::class, 'storemanageremployee'])->name('employee.storemanageremployeedetail');

Route::prefix('employee')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('data', [EmployeeController::class, 'getEmployees'])->name('employee.data');
    Route::get('create', [EmployeeController::class, 'create'])->name('employee.create');
    Route::post('store', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('{employee_id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('{employee_id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::post('{employee_id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    Route::get('createmanageremployees', [EmployeeController::class, 'createmanageremployees'])->name('employee.createmanageremployees');
    Route::get('manageremployeeindex', [EmployeeController::class, 'manageremployeeindex'])->name('employee.manageremployeeindex');
    Route::get('manageremployeedata', [EmployeeController::class, 'manageremployeedata'])->name('employee.manageremployeedata');
    Route::get('lhs_report/{id}', [EmployeeController::class, 'lhs_report'])->name('employee.lhs_report');
   
    Route::get('show_mom/{leadId}', [EmployeeController::class,'show_mom'])->name('employee.show_mom');
    Route::post('create_mom/', [EmployeeController::class,'create_mom'])->name('employee.create_mom');
    Route::get('export/{id}/word_single_down', [EmployeeController::class,'wordEmployeeDownSingle'])->name('wordEmployeeDownSingle');

});

Route::post('lhs_report_save', [EmployeeController::class,'lhs_report_save'])->name('employee.lhs_report_save');
Route::get('lhs_report/edit/{id}', [EmployeeController::class,'edit_lhs_report'])->name('employee.edit_lhs_report');
Route::post('lhs_report/update', [EmployeeController::class,'update_lhs_report'])->name('employee.update_lhs_report');
Route::get('lhs_report/view_lhs/{id}', [EmployeeController::class,'view_lhs'])->name('employee.view_lhs');
Route::post('create_mom', [EmployeeController::class,'create_mom'])->name('employee.create_mom');


Route::prefix('leads')->group(function (): void {
    Route::get('data', action: [LeadsController::class, 'getSourceLeads'])->name('source.leads.data');
    // Route::get('/{source_id?}', [LeadsController::class, 'index'])->name('leads.index');

    Route::get('create', [LeadsController::class, 'create'])->name('leads.create');
    Route::post('store', [LeadsController::class, 'store'])->name('leads.store');
    Route::get('assign_lead_emp/{id?}', [LeadsController::class, 'assign_lead_emp'])->name('leads.assign_lead_emp');
    Route::post('statusUpdate', [EmployeeController::class, 'statusUpdate'])->name('employee.statusUpdate');
    Route::post('manageemployeelogin', [EmployeeController::class, 'manageemployeelogin'])->name('employee.manageemployeelogin');

    Route::get('campname', [LeadsController::class, 'campname'])->name('leads.campname');
    Route::get('assigned_leads', [LeadsController::class, 'assigned_leads'])->name('leads.assigned_leads');

    Route::post('assingParticalurleads', [LeadsController::class, 'assingParticalurleads'])->name('leads.assingParticalurleads');
    Route::get('unassigned', [LeadsController::class, 'unassigned'])->name('leads.unassigned');

    Route::get('reassigned', [LeadsController::class, 'reassigned'])->name('leads.reassigned');
    Route::get('unapproved_manager_leads_list_pagination', [LeadsController::class, 'unapprovedManagerLeadsajaxPagination'])->name('unapproved_manager_leads_list_pagination');
    Route::get('unapproved_emp_leads_list_pagination', [LeadsController::class, 'unapproved_emp_leads_list_pagination'])->name('unapproved_emp_leads_list_pagination');

    Route::get('unapprovedLeads', [LeadsController::class, 'unapprovedLeads'])->name('leads.unapprovedLeads');
    Route::post('updateApprovalStatus', [LeadsController::class, 'updateApprovalStatus'])->name('updateApprovalStatus');
    Route::get('unapprovedLeadsemp', [LeadsController::class, 'unapprovedLeadsemp'])->name('leads.unapprovedLeadsemp');

    Route::get('closed', [LeadsController::class, 'closed'])->name('closed');
    Route::get('failed', [LeadsController::class, 'failed'])->name('failed');
    Route::get('in_progress', [LeadsController::class, 'in_progress'])->name('in_progress');
    Route::post('add_note', [LeadsController::class,'add_note'])->name('add_note');
    Route::get('notes_view/{id}', [LeadsController::class,'notes_view'])->name('notes_view');
    Route::get('delete/{id}', [LeadsController::class,'delete'])->name('delete');
    Route::get('assign', [LeadsController::class,'assign'])->name('assign');
    Route::post('assignLeadsManager', [LeadsController::class,'assignLeadsManager'])->name('assignLeadsManager');
    Route::get('employeeclosedleads', [SourcesController::class,'employeeclosedleads'])->name('employeeclosedleads');


});

Route::get('/reminder/view', [ReminderController::class,'view']);
Route::post('changeStatus', [LeadsController::class,'changeStatus'])->name('changeStatus');


Route::get('man_daily_report', [EmployeeController::class, 'man_daily_report'])->name('employee.man_daily_report');
Route::get('empdailyreport', [EmployeeController::class, 'empdailyreport'])->name('employee.empdailyreport');


Route::post('allocatedCompaign', [EmployeeController::class, 'allocatedCompaign'])->name('allocatedCompaign');
Route::get('getLeadsData', [EmployeeController::class, 'getLeadsData'])->name('getLeadsData');
Route::get('employee/man_daily_report', [DailyReportController::class, 'man_daily_report'])->name('employee.man_daily_report');
Route::get('employee/{id}/daily_report', [DailyReportController::class, 'daily_report'])->name('employee.daily_report');

Route::get('/add_leads/{id}', [SourcesController::class,'create_campaign'])->name('create_campaign');
Route::post('import_leads', [SourcesController::class,'import_leads'])->name('import_leads');
Route::get('source-lead/{id}', [SourcesController::class,'getLeadBySourceId']);
Route::get('/campaign/camp_assign_emp', [SourcesController::class,'camp_assign_emp'])->name('camp_assign_emp');
Route::get('/campaign/camp_assign_list', [SourcesController::class,'camp_assign_list'])->name('camp_assign_list');
Route::get('/campaign/camp_assign_emp/{id}', [SourcesController::class,'view_camp'])->name('employe.view_camp');
Route::get('/leadslist', [SourcesController::class,'leadslist'])->name('leadslist');

Route::get('/sources/{id}/leadview', [LeadsController::class,'leadview'])->name('leadview');
Route::get('getsourceslead', [LeadsController::class,'getLeadsData'])->name('getsourceslead');

Route::get('leads/{id}', [LeadsController::class,'showlead'])->name('showlead');
Route::get('editlead/{id}', [LeadsController::class,'edit'])->name('editlead');
Route::patch('updatelead/{id}', [LeadsController::class,'update'])->name('leads.update');


Route::get('lead/exportCsv/{id}/report_down', [LeadClosedCsv::class,'reportDown'])->name('reportDown');

Route::get('test', [PostController::class, 'index']);
Route::get('tables', [PostController::class, 'datatables'])->name('test.datatables');

require __DIR__ . '/auth.php';