<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Models\TrafficViolation;
use App\Models\ApprehendingOfficer;
use App\Models\TasFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use App\Models\admitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\fileviolation;
use App\Models\G5ChatMessage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Models\archives;
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
    return view('welcome');
})->name('landpage');

Route::get('/loginpage', [AuthController::class, 'loadlogin'])->name('login');
Route::post('/loginpost', [AuthController::class, 'login'])->name('login.submit'); 
Route::get('/logout', [AuthController::class, 'logoutx'])->name('logout');

// Middleware routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'indexa'])->name('dashboard');

    Route::get('/officers', [DashboardController::class, 'filterOfficer'])->name('officers.index');
    Route::post('/get-violations', [DashboardController::class, 'getViolations'])->name('get.today');

    // =============== ANALYTICS 

    Route::get('/analytics', [DashboardController::class, 'analyticsDash'])->name('analytics.index');
    Route::get('/getChartData', [DashboardController::class, 'getChartData']);
   
    
    Route::get('/officers/{departmentName}', [DashboardController::class, 'getByDepartmentName']);
 
    Route::get('/documents/print/{id}', [DashboardController::class, 'printsub'])->name('print.sub');
    Route::get('/contested.cases/reports', [DashboardController::class, 'reportsview'])->name('filterByMonth');

    Route::get('/yearly-data/{year}', [DashboardController::class, 'showYearData'])->name('showYearData');

    Route::get('/officers-chart', [UserController::class, 'officersChart'])->name('officers.chart');


    // =============== USER PROFILE

    Route::get('users/{id}/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('users/{id}/profile/edit', [DashboardController::class, 'edit'])->name('profile.edit');
    Route::put('users/{id}/profile/update', [DashboardController::class, 'update'])->name('profile.update');
    Route::get('users/{id}/profile/change_password', [DashboardController::class, 'change'])->name('profile.change');
    Route::post('users/{id}/profile/update_password', [DashboardController::class, 'updatePassword'])->name('profile.update_password');
    Route::post('users/{id}/profile/profile.picture.save', [DashboardController::class, 'updatePicture'])->name('profile.picture.upload');

    
  // =============       USER-MANAGEMENT

  Route::get('/manage-user', [DashboardController::class, 'management'])->name('user_management');
  Route::get('/manage-user/users/{id}/edit', [DashboardController::class, 'edit'])->name('users.edit');
  Route::get('/manage-user/users/{id}/profile', [DashboardController::class, 'profile'])->name('users.profile');
  Route::delete('/manage-user/users/{user}', [DashboardController::class, 'userdestroy'])->name('users.destroy');
  Route::get('/manage-user/add-user', [DashboardController::class, 'add_user'])->name('add.user');
  Route::post('/manage-user/store-user', [DashboardController::class, 'store_user'])->name('store.user');

 //                     USER-MANAGEMENT============

    // =============    AO/VIOLATION

    Route::get('/department/', [DashboardController::class, 'department'])->name('see.dep');
    Route::post('/department/save.department', [DashboardController::class, 'departmentsave'])->name('save.deps');
    Route::get('/department/edit', [DashboardController::class, 'editdepp'])->name('edit.deps');
    Route::put('/department/{id}/update', [DashboardController::class, 'updatedeps'])->name('deps.update');

    Route::get('/apprehendingofficer', [DashboardController::class, 'officergg'])->name('see.offi');
    Route::get('/apprehendingofficer/editofficer', [DashboardController::class, 'editoffi'])->name('edit.offi');
    Route::post('/apprehendingofficer/store.officer', [DashboardController::class, 'save_offi'])->name('save.offi');
    Route::put('/apprehendingofficer/officers/{id}', [DashboardController::class, 'updateoffi'])->name('officers.update');
    Route::put('/edit/{id}/violation', [DashboardController::class, 'updateviolation'])->name('edit.violation');
    Route::get('/violation', [DashboardController::class, 'violationadd'])->name('see.vio');
    Route::post('/violation/save.violation', [DashboardController::class, 'addvio'])->name('add.violation');
    Route::get('/edit/violation', [DashboardController::class, 'edivio'])->name('edit.vio');

    Route::get('edit/violation/details/{id}', function ($id) {
        $violation = TrafficViolation::findOrFail($id);
        return view('ao.detailsviolation', compact('violation'));
    })->name('fetchingviolation');
    Route::get('officer/details/{id}', function ($id) {
        $officer = ApprehendingOfficer::findOrFail($id);
        return view('ao.detailsoffi', compact('officer'));
    })->name('fetchingofficer');

    //                  AO/VIOLATION    =============  
    Route::get('/officers', [DashboardController::class, 'tableofficerdash'])->name('officers.index');
    // archives  =====
    
    Route::get('/archives/add', [DashboardController::class, 'archivesmanage'])->name('archivesmanage');
    Route::post('/archives/save', [DashboardController::class, 'archivessubmit'])->name('archivessubmit');
    Route::get('/archives/view', [DashboardController::class, 'archivesview'])->name('archivesview');
    Route::get('/archives/view-{id}/details/', [DashboardController::class, 'detailsarchives'])->name('detailsarchives');
    Route::post('/archives/view/details/save.remarks', [DashboardController::class, 'saveRemarksarchives'])->name('saveRemarksarchives');
    Route::post('/archives/view-{id}/details/finish-case', [DashboardController::class, 'finishCase_archives'])->name('finishCase_archives');
    Route::post('/archives/view-{id}/details/update', [DashboardController::class, 'updateStatusarchives'])->name('updateStatusarchives');
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    Route::get('/archives/edit', [DashboardController::class, 'updatearchives'])->name('updatearchives');
    Route::get('/archives/editview-{id}/details/', [DashboardController::class, 'detailsarchivesedit'])->name('detailsarchivesedit');
    Route::put('/archives/editview-{id}/details/update.details', [DashboardController::class, 'updatedetailarchives'])->name('updatedetailarchives');
    Route::delete('/archives/editview-{id}/details/deleteViolation', [DashboardController::class, 'delete_edit_violation'])->name('delete_edit_violation');

    Route::post('/archives/editview/details/delete-remark/',  [DashboardController::class, 'deleteRemark_archives'])->name('deleteRemark_archives');
    Route::post('/archives/editview-{id}/details/update.violation', [DashboardController::class, 'update_archive_violation'])->name('update_archive_violation');
    Route::delete('/archives/editview-{id}/details/delete.violation', [DashboardController::class, 'delete_archives'])->name('delete_archives_case');
    Route::delete('/archives/edit-{id}/violations/remove-attachment', [DashboardController::class, 'removeAttachmentarchives'])->name('removeAttachmentarchives');

    //  =====archives  //

    // =============== ADMIT CASES

    Route::get('/admitTAS', [DashboardController::class, 'admitview'])->name('admitted.view');
    Route::get('/admitTAS/admit.manageform', [DashboardController::class, 'admitmanage'])->name('admitted.manage');
    Route::post('/admitTAS/admit.manageform', [DashboardController::class, 'admittedsubmit'])->name('admittedsubmit.tas');
    Route::post('/admitTAS/admit-remarks', [DashboardController::class, 'admitremark'])->name('admitremark');
    Route::put('/admitTAS/admitted-cases/{id}', [DashboardController::class, 'updateAdmittedCase'])->name('admitted-cases.update');
    Route::get('/admitTAS/AdmittedEdit', [DashboardController::class, 'editAdmit'])->name('edit.admit');
    Route::post('/admitTAS/view-{id}/details/update', [DashboardController::class, 'updateStatusadmitted'])->name('updateStatusadmitted');
    Route::post('/admitTAS/view-{id}/details/finish-case', [DashboardController::class, 'finishCase_admitted'])->name('finishCase_admitted');
    
    Route::get('/admitTAS/admit/details/{id}', [DashboardController::class, 'detailsadmitted'])->name('fetchingadmitted');
    //------------ MISSING UPDATE CASES
    Route::get('/admitTAS/edit', [DashboardController::class, 'updateAdmitted'])->name('update.admit.index');
    Route::get('/admitTAS/editview-{id}/details/', [DashboardController::class, 'detailsadmittededit'])->name('detailsadmittededit');
    Route::put('/admitTAS/editview-{id}/details/update.details', [DashboardController::class, 'updatedetailadmitted'])->name('updatedetailadmitted');
    Route::delete('/admitTAS/editview-{id}/details/deleteViolation', [DashboardController::class, 'delete_edit_violation_admitted'])->name('delete_edit_violation_admitted');
    Route::post('/admitTAS/editview/details/delete-remark/',  [DashboardController::class, 'deleteRemark_admitted'])->name('deleteRemark_admitted');
    Route::delete('/admitTAS/editview-{id}/details/delete.violation', [DashboardController::class, 'delete_admitted'])->name('delete_admitted_case');
    Route::delete('/admitTAS/edit-{id}/violations/remove-attachment', [DashboardController::class, 'removeAttachmentadmitted'])->name('removeAttachmentadmitted');
    //                 ADMIT CASES ===============

    // ====================tasFile 
    Route::post('{id}/upload-file-contest', [DashboardController::class, 'uploadFileTas'])->name('upload.file.contest');
    Route::post('{id}/upload-file-admit', [DashboardController::class, 'uploadFileAdmit'])->name('upload.file.admit');
    Route::get('/contested/manageTAS', [DashboardController::class, 'tasManage'])->name('tas.manage');
    Route::post('/contested/manageTAS/save', [DashboardController::class, 'submitForm'])->name('submitForm.tas');
    Route::get('/contested/viewTAS', [DashboardController::class, 'tasView'])->name('tas.view');
    Route::get('/contested/viewTAS/tasfile-{id}/details/view', [DashboardController::class, 'detailstasfile'])->name('fetchingtasfile');
    Route::post('/contested/viewTAS/tasfile/details/save-remarks', [DashboardController::class, 'saveRemarks'])->name('save.remarks');
    Route::post('/contested/viewTAS/tasfile-{id}/details/update-status', [DashboardController::class, 'updateStatus'])->name('update.status');
    Route::post('/contested/viewTAS/tasfile-{id}/details/finish.case', [DashboardController::class, 'finishCase'])->name('finish.case');
    // -------------------------------- end of viewtas ------------------------------------------------------------------------------------
    Route::get('/contested/edit/view', [DashboardController::class, 'updateContest'])->name('update.contest.index');
    Route::get('/contested/edit/tasfile-{id}/view/details', [DashboardController::class, 'detailsedit'])->name('fetchingeditfile');
    Route::put('/contested/edit/tasfile-{id}/view/details/update', [DashboardController::class, 'updateTas'])->name('violations.updateTas');
    Route::post('/contested/edit/tasfile-{id}/view/details/updateViolation', [DashboardController::class, 'UPDATEVIO'])->name('edit.updatevio');
    Route::delete('/contested/edit/tasfile-{id}/view/details/deleteViolation', [DashboardController::class, 'DELETEVIO'])->name('edit.viodelete');
    Route::delete('/contested/edit/tasfile-{id}/view/details/remove-attachment', [DashboardController::class, 'removeAttachment'])->name('tasfile.removeAttachment');
    Route::post('/contested/edit/tasfile/view/details/delete-remark/',  [DashboardController::class, 'deleteRemark'])->name('edit.deleteremarks');
    Route::delete('/contested/edit/tasfile-{id}/view/details/deleteCase', [DashboardController::class, 'deleteTas'])->name('violations.delete');
    //     tasFile =============================

    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////ANALYTICS
/////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////
//////////////////////////////////////
Route::get('/monthly-type-of-vehicle', [DashboardController::class, 'fetchMonthlyTypeOfVehicle'])->name('sakotse');
Route::get('/api/pie-chart-data', [DashboardController::class, 'getPieChartData'])->name('piechartngContested');



Route::get('/api/date-received-data', [DashboardController::class, 'getDateReceivedData'])->name('bilangngmgakaso');
 
Route::get('/fetch-violations', [DashboardController::class, 'fetchViolations'])->name('damingmgakaso');


Route::get('/api/violation-rankings', [DashboardController::class, 'getViolationRankings'])->name('violation.rankings');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////// COMMUNICATION  /////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::post('/chatstore/storeChat', [DashboardController::class, 'storeMessage'])->name('chat.store');
Route::get('/get-chat-data/{userId}', [DashboardController::class, 'getChatData'])->name('chat.messages');
Route::get('/chat/{userId?}', [DashboardController::class, 'chatIndex'])->name('chat.index');


Route::get('/user/{user}/messages', [UserController::class, 'getUserMessages']);
 
Route::get('/start-chat/{userId}', [UserController::class, 'startChat'])->name('chat.start');
Route::get('/check-new-messages/{userId}',  [DashboardController::class, 'checkNewMessages'])->name('check.chat');

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////                     ////////////////////////////////////////////////////
    /////////////////////////////////////                HISTORY             ///////////////////////////////////////////
    ///////////////////////////////////////////                        ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
  
    Route::get('/history', [UserController::class, 'admitTrail'])->name('audit.trail');

    Route::get('/admitted/{id}/filter/{filter?}', [UserController::class, 'show']);
    Route::get('/notifications', [UserController::class, 'getNotifications'])->name('notifications');
 
});


Route::get('/subpoena', function () { 
    // Get the month parameter from the request, default to current month if not provided

    // $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
    $selectedMonth = "2023-12";

    // Determine the start and end dates of the selected month
    $startDate = Carbon::parse($selectedMonth . '-01')->startOfMonth();
    $endDate = Carbon::parse($selectedMonth . '-01')->endOfMonth();

    // Query TasFiles with date range
    $tasFiles = TasFile::whereBetween('date_received', [$startDate, $endDate])->get();

    // Process each TasFile to attach related violations
    foreach ($tasFiles as $tasFile) {
        $violations = json_decode($tasFile->violation);
        if ($violations) {
            if (is_array($violations)) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            } else {
                $relatedViolations = TrafficViolation::where('code', $violations)->get();
            }
        } else {
            $relatedViolations = collect(); // Empty collection if no violations
        }
        $tasFile->relatedViolations = $relatedViolations;
    }

    // Format monthYear based on the selected month
    $monthYear = strtoupper(Carbon::parse($selectedMonth)->format('F Y'));

    return view('subpoena', ['tasFiles' => $tasFiles, 'monthYear' => $monthYear]);
});

// Staff routes
Route::group(['prefix' => 'user', 'middleware' => ['web', 'isUser']], function () {
    // Define user-specific routes here
});
?>
