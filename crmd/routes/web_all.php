<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController,
    LeadsController,
    EmployeesController,
    ProfileController,
    InstallmentsController,
    FeedbacksController,
    NotesController,
    ReminderController,
    SourcesController,
    ImportExportController,
    AjaxController,
    LeadClosedController,
    LeadClosedCsv,
    DailyReportController,
    ManagersController
};

// Web Routes
Route::get('/', fn() => redirect('login'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/allCounts', [HomeController::class, 'allCounts'])->name('allCounts');
Route::get('/employeesPerformance', [HomeController::class, 'employeesPerformance'])->name('employeesPerformance');
Route::post('/getEmployeesPerformance', [HomeController::class, 'getEmployeesPerformance'])->name('getEmployeesPerformance');
Route::get('/news/{id}', [HomeController::class, 'news'])->name('news');
Route::get('/new', [HomeController::class, 'new'])->name('new');
Route::get('/performanceMonthly', [HomeController::class, 'performanceMonthly'])->name('performanceMonthly');
Route::get('/performanceYearly', [HomeController::class, 'performanceYearly'])->name('performanceYearly');
Route::get('/analysis', [HomeController::class, 'analysis']);
Route::get('/employees_performance', [HomeController::class, 'employees_performance'])->name('employees_performance');
Route::get('/performance', [HomeController::class, 'performance']);
Route::get('/performanceExternalManager', [HomeController::class, 'externalManagerPerformance'])->name('externalManager.performance');
Route::get('/all-leads', [HomeController::class, 'externalManagerAlleads'])->name('all.leads');
Route::get('/get_notifications', [HomeController::class, 'get_notifications'])->name('get_notifications');

Route::prefix('leads')->name('leads.')->group(function () {
    Route::post('/searchLead', [LeadsController::class, 'searchLead'])->name('searchLead');
    Route::get('/view', [LeadsController::class, 'views'])->name('getViewLeads');
    Route::get('/unassigned', [LeadsController::class, 'Unassigned'])->name('unassigned');
    Route::get('/reassigned', [LeadsController::class, 'Reassigned'])->name('reassigned');
    Route::post('/getLeads', [LeadsController::class, 'getLeads'])->name('getLeads');
    Route::post('/assign', [LeadsController::class, 'assigns'])->name('assign');
    Route::post('/assingParticalurleads', [LeadsController::class, 'assingParticalurleads'])->name('assingParticalurleads');
    Route::post('/changeStatus', [LeadsController::class, 'changeStatus'])->name('changeStatus');
    Route::post('/updateApprovalStatus', [LeadsController::class, 'updateApprovalStatus'])->name('updateApprovalStatus');
    Route::get('/closed', [LeadsController::class, 'closed']);
    Route::get('/failed', [LeadsController::class, 'failed']);
    Route::get('/unapproved', [LeadsController::class, 'unapproved']);
    Route::get('/unapproved_leads', [LeadsController::class, 'unapprovedLeads']);
    Route::get('/assigned_leads', [LeadsController::class, 'assigned_leads'])->name('assigned_leads');
    Route::get('/delete/{id}', [LeadsController::class, 'delete']);
    Route::post('/assignLeadsEmployee', [LeadsController::class, 'assignLeadsEmployee'])->name('assignLeadsEmployee');
    Route::post('/assignLeadsManager', [LeadsController::class, 'assignLeadsManager'])->name('assignLeadsManager');
    Route::post('/assignHalfLeadsEmployee', [LeadsController::class, 'assignHalfLeadsEmployee'])->name('assignHalfLeadsEmployee');
    Route::post('/add_note', [LeadsController::class, 'add_note'])->name('add_note');
    Route::get('/campname', [LeadsController::class, 'campname'])->name('campname');
    Route::get('/test_fn', [LeadsController::class, 'test_fn'])->name('test_fn');
    Route::get('/source/{id}/leadview', [LeadsController::class, 'leadview'])->name('leadview');
    Route::post('/searchInstallment', [InstallmentsController::class, 'searchInstallment'])->name('searchInstallment');
    Route::get('/installments/view/{id}', [InstallmentsController::class, 'view']);
    Route::get('/feedbacks/add/{id}', [FeedbacksController::class, 'add']);
    Route::get('/notes/add/{id}', [NotesController::class, 'add']);
    Route::get('/notes/in_progress', [NotesController::class, 'in_progress'])->name('in_progress');
    Route::get('/notes/view/{id}', [NotesController::class, 'view']);
    Route::get('/reminder/view', [ReminderController::class, 'view']);
    Route::get('/notes_new', [EmployeesController::class, 'notes_new'])->name('employee.notes_new');
    Route::get('/emp_daily_report', [EmployeesController::class, 'emp_daily_report'])->name('employee.emp_daily_report');
    Route::get('/man_daily_report', [EmployeesController::class, 'man_daily_report'])->name('employee.man_daily_report');
    Route::get('/empdailyReportPagination', [EmployeesController::class, 'empdailyReportPagination'])->name('empdailyReportPagination');
    Route::get('/man_daily_report_ajax_pagination', [EmployeesController::class, 'dailyReportPagination'])->name('dailyReportPagination');
    Route::get('/notes/{id}/delete', [NotesController::class, 'delete']);
});

Route::prefix('employees')->name('employees.')->group(function () {
    Route::get('/addManagerEmployee', [EmployeesController::class, 'addManagerEmployee'])->name('addManagerEmployee');
    Route::get('/editManagerEmployee/{id}/edit', [EmployeesController::class, 'editManagerEmployee'])->name('editManagerEmployee');
    Route::patch('/updateManagerEmployee/{id}', [EmployeesController::class, 'updateManagerEmployee'])->name('updateManagerEmployee');
    Route::get('/delete/{id}', [EmployeesController::class, 'delete']);
    Route::get('/{id}/performace', [EmployeesController::class, 'performace']);
    Route::get('/{id}/show_mom', [EmployeesController::class, 'show_mom'])->name('show_mom');
    Route::post('/create_mom', [EmployeesController::class, 'create_mom'])->name('create_mom');
    Route::post('/allocatedCompaign', [EmployeesController::class, 'allocatedCompaign'])->name('allocatedCompaign');
    Route::get('/statusUpdate/{id}', [EmployeesController::class, 'statusUpdate']);
    Route::get('/lhs_report/{id}', [EmployeesController::class, 'lhs_report'])->name('employee.lhs_report');
    Route::post('/lhs_report_save', [EmployeesController::class, 'lhs_report_save'])->name('employee.lhs_report_save');
    Route::get('/lhs_report/edit/{id}', [EmployeesController::class, 'edit_lhs_report'])->name('employee.edit_lhs_report');
    Route::post('/lhs_report/update', [EmployeesController::class, 'update_lhs_report'])->name('employee.update_lhs_report');
    Route::get('/lhs_report/view_lhs/{id}', [EmployeesController::class, 'view_lhs'])->name('employee.view_lhs');
});

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/update', [ProfileController::class, 'profileUpdate'])->name('update');
});

Route::prefix('installments')->name('installments.')->group(function () {
    Route::get('/', [InstallmentsController::class, 'installment']);
    Route::get('/view/{id}', [InstallmentsController::class, 'view']);
});

Route::prefix('feedbacks')->name('feedbacks.')->group(function () {
    Route::post('/getFeedbacks', [FeedbacksController::class, 'getFeedbacks'])->name('getFeedbacks');
    Route::get('/add/{id}', [FeedbacksController::class, 'add']);
});

Route::prefix('notes')->name('notes.')->group(function () {
    Route::get('/add/{id}', [NotesController::class, 'add']);
});