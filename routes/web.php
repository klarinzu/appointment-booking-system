<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SummerNoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumateClearanceController;
use App\Http\Controllers\DocumateHandbookController;
use App\Http\Controllers\DocumateTransactionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/',[FrontendController::class,'index'])->name('home');

Route::middleware(['auth'])->group(function () {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/scheduled-calendar', [DashboardController::class, 'calendar'])->name('documate.calendar.index');
Route::get('/dashboard/transaction-calendar', [DashboardController::class, 'transactionCalendar'])->name('dashboard.transaction-calendar');
Route::post('/chat', ChatController::class)->name('chat.send');
Route::get('/transactions', [DocumateTransactionController::class, 'index'])->name('documate.transactions.index');
Route::post('/transactions', [DocumateTransactionController::class, 'store'])->name('documate.transactions.store');
Route::get('/transactions/export', [DocumateTransactionController::class, 'export'])->name('documate.transactions.export');
Route::get('/transactions/examples/{transactionType}', [DocumateTransactionController::class, 'exampleForm'])->name('documate.transactions.example');
Route::get('/transactions/{transaction}', [DocumateTransactionController::class, 'show'])->name('documate.transactions.show');
Route::post('/transactions/{transaction}/approve', [DocumateTransactionController::class, 'approve'])->name('documate.transactions.approve');
Route::patch('/transactions/{transaction}/status', [DocumateTransactionController::class, 'updateStatus'])->name('documate.transactions.status.update');
Route::post('/transactions/{transaction}/schedule', [DocumateTransactionController::class, 'scheduleAppointment'])->name('documate.transactions.schedule');
Route::get('/transactions/{transaction}/form', [DocumateTransactionController::class, 'form'])->name('documate.transactions.form');
Route::get('/transactions/{transaction}/download', [DocumateTransactionController::class, 'downloadForm'])->name('documate.transactions.download');
Route::get('/clearances', [DocumateClearanceController::class, 'index'])->name('documate.clearances.index');
Route::patch('/clearances/{studentProfile}', [DocumateClearanceController::class, 'update'])->name('documate.clearances.update');
Route::get('/handbook', [DocumateHandbookController::class, 'index'])->name('documate.handbook.index');

    //user
    Route::resource('user',UserController::class)->middleware('permission:users.view|users.create|users.edit|users.delete');
    //update user password

    //profile page
    Route::get('profile',[ProfileController::class,'index'])->name('profile');
    //user profile update
    Route::patch('profile-update/{user}',[ProfileController::class,'profileUpdate'])->name('user.profile.update');
    Route::patch('user/pasword-update/{user}',[UserController::class,'password_update'])->name('user.password.update');
    Route::put('user/profile-pic/{user}',[UserController::class,'updateProfileImage'])->name('user.profile.image.update');

    //delete profile image
    Route::patch('delete-profile-image/{user}',[UserController::class,'deleteProfileImage'])->name('delete.profile.image');
    //trash view for users
    Route::get('user-trash', [UserController::class, 'trashView'])->name('user.trash');
    Route::get('user-restore/{id}', [UserController::class, 'restore'])->name('user.restore');
    //deleted permanently
    Route::delete('user-delete/{id}', [UserController::class, 'force_delete'])->name('user.force.delete');

    Route::get('settings', [SettingController::class, 'index'])->name('setting')->middleware('permission:settings.edit');
    Route::post('settings/{setting}', [SettingController::class, 'update'])->name('setting.update');


    Route::resource('category', CategoryController::class)->middleware('permission:categories.view|categories.create|categories.edit|categories.delete');


    // Services
    Route::resource('service', ServiceController::class)->middleware('permission:services.view|services.create|services.edit|services.delete');
    Route::get('service-trash', [ServiceController::class, 'trashView'])->name('service.trash');
    Route::get('service-restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
    //deleted permanently
    Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])->name('service.force.delete');


    //summernote image
    Route::post('summernote',[SummerNoteController::class,'summerUpload'])->name('summer.upload.image');
    Route::post('summernote/delete',[SummerNoteController::class,'summerDelete'])->name('summer.delete.image');


    //employee
    // Route::resource('user',UserController::class);
    Route::get('employee-booking',[UserController::class,'EmployeeBookings'])->name('employee.bookings');
    Route::get('my-booking/{id}',[UserController::class,'show'])->name('employee.booking.detail');

    // employee profile self data update
    Route::patch('employe-profile-update/{employee}',[ProfileController::class,'employeeProfileUpdate'])->name('employee.profile.update');

    //employee bio
    Route::put('employee-bio/{employee}',[EmployeeController::class,'updateBio'])->name('employee.bio.update');


    // Route::get('/login', function () {
    //     return view('auth.login');
    // });

    Route::get('test',function(Request $request){
        return view('test',  [
            'request' => $request
        ]);
    });



    Route::post('test', function (Request $request) {
        return response()->json($request->all());
    })->name('test');

});



//frontend routes
//fetch services from categories
Route::get('/categories/{category}/services', [FrontendController::class, 'getServices'])->name('get.services');

//fetch employee from category
Route::get('/services/{service}/employees', [FrontendController::class, 'getEmployees'])->name('get.employees');

//get availibility
Route::get('/employees/{employee}/availability/{date?}', [FrontendController::class, 'getEmployeeAvailability'])
    ->name('employee.availability');

//create appointment
Route::post('/bookings', [AppointmentController::class, 'store'])->name('bookings.store');
Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments')->middleware('permission:appointments.view|appointments.create|appointments.edit|appointments.delete');

Route::post('/appointments/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update.status');

//update status from dashbaord
Route::post('/update-status', [DashboardController::class, 'updateStatus'])->name('dashboard.update.status');
