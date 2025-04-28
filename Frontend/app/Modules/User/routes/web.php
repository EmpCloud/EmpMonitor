<?php
use App\Modules\User\Controllers\UserController;
use App\Modules\User\Controllers\UserDetailsController;
use App\Modules\User\Controllers\EmployeeController;
use App\Modules\User\Controllers\EmployeeDetailsController;
use App\Modules\User\Controllers\TimeAttendanceController;

Route::group(['module' => 'User', 'middleware' => ['web'], 'namespace' => 'App\Modules\User\Controllers'], function () {
    Route::get('/login', [UserController::class,'login']);
    Route::post('/login', [UserController::class,'login']);
    Route::get('/admin-login', [UserController::class,'adminLogin'])->middleware('openAdmin');
    Route::post('/admin-login', [UserController::class,'adminLogin'])->middleware('openAdmin');;
    Route::get('/loginpageWhitelabel/{username?}/{password?}', [UserController::class,'loginpageWhitelabel']);
    
    Route::group(['middleware' => ['authenticateAdmin']], function () {
        Route::group(array('prefix' => '{role}'), function ($role) {
            Route::get('/permission-denied', [UserController::class,'permissionDenied']);
            Route::any('/license-count-exceed', [UserController::class,'licenseCountExceed']);
            Route::post('/Emp-Delete', [UserController::class,'Employeedelete']);
            Route::get('/logout', [UserController::class,'logout'])->name('logout');
            Route::post('/register-Employee', [UserController::class,'EmployeeRegistration']);
            Route::get('/EmployeeDetail', [UserController::class,'EmployeeDetails']);
            Route::get('/get-employee-details', [UserDetailsController::class,'employeeFullDetailsPage'])->name('getEmployeeDetails');
            Route::get('/show_details', [UserController::class,'show_details']);
            Route::post('/show_details', [UserController::class,'show_details']);
            Route::post('/get-web-app-history', [UserDetailsController::class,'getWebAppHistory']);
            Route::get('/employee-details', [UserController::class,'EmpDetails'])->name('employee-details');
            Route::get('/dashboard', [UserController::class,'dashboard'])->name('dashboard');
            Route::post('/get-time-sheets-data', [UserDetailsController::class,'getTimeSheetData']);
            Route::get('/attendance-history', [TimeAttendanceController::class,'attendanceHistory'])->name('attendance-history');
            Route::post('/attendance-history', [TimeAttendanceController::class,'attendanceHistory']);
            Route::post('/Delete-multiple', [UserController::class,'DeleteMultiple']);
            Route::post('/Emp-edit', [UserController::class,'editEmployee']);
            }); 
        });


        Route::group(['middleware' => ['authenticateEmployee']], function () {
            Route::group(array('prefix' => '{role}'), function ($role) {
                Route::get('/myTimeline', [EmployeeController::class,'employeeFullDetailsPage'])->name('myTimeline');
                Route::get('/employee-logout', [EmployeeController::class,'logoutEmployee'])->name('employee-logout');
                Route::post('/get-web-app-histories', [UserDetailsController::class,'getWebAppHistory']);
                Route::post('/get-time-sheets-data-employee', [UserDetailsController::class,'getTimeSheetData']);
                Route::get('/attendance-history-employee', [TimeAttendanceController::class,'attendanceHistoryEmployee'])->name('attendance-history-employee');
                Route::post('/attendance-history-employee', [TimeAttendanceController::class,'attendanceHistoryEmployee']);
            });
        });

});




