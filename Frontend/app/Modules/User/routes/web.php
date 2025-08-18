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
            Route::get('/manageLocations', [UserController::class,'manageLocations'])->name('manageLocations');
            Route::post('/add-location', [UserController::class,'addLoctation']);
            Route::post('/delete-location', [UserController::class,'deleteLocation']);
            Route::post('/update-location', [UserController::class,'updateLocation']);

            Route::get('/manageDepartment', [UserController::class,'manageDepartment'])->name('manageDepartment');
            Route::post('/add-department', [UserController::class,'addDepartment']);
            Route::post('/delete-department', [UserController::class,'deleteDepartment']);
            Route::post('/update-department', [UserController::class,'updateDepartment']);
            Route::get('/reports', [UserController::class,'getReports'])->name('reports');
            Route::post('/get-report-data', [UserController::class,'getReportData'])->name('getReportData');
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




