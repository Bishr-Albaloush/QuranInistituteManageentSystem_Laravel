<?php

use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RecitingController;
use App\Http\Controllers\Api\StudentController;
use App\Models\Permission;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\Additional_PointsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['api', 'checkpassword']],function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('move_attendance',[PersonController::class, 'move_attendance']);
    
    Route::group(['middleware' => ['auth.guard:user-api']],function () {
    
        Route::post('logout', [AuthController::class, 'logout']);
        
        Route::group(['middleware' => ['is.add_person']],function () {
            Route::post('add_person', [PersonController::class, 'add_person']);
            Route::post('push_data', [PersonController::class, 'get_data']);
            Route::post('create_pages', [RecitingController::class, 'create_pages']);
            Route::post('push_recites', [RecitingController::class, 'push_recites']);
        });

        Route::group(['middleware' => ['is.view_people']],function(){
            Route::post('view_all_people', [PersonController::class, 'view_all_people']);
            Route::post('ViewAllPeople', [PersonController::class, 'ViewAllPeople']);
            Route::post('filter', [PersonController::class, 'filter']);

        });

        Route::group(['middleware' => ['is.view_person']],function () {
            Route::post('view_person', [PersonController::class, 'view_person']); 
            Route::post('view_all_people_without_religion', [PersonController::class, 'view_all_people_without_religion']);
            Route::post('view_supervisors', [PersonController::class, 'view_supervisors']);
            Route::post('view_moderators', [PersonController::class, 'view_moderators']);
            Route::post('view_assistants', [PersonController::class, 'view_assistants']);
            Route::post('view_testers', [PersonController::class, 'view_testers']);
        });

        Route::group(['middleware' => ['is.view_group']],function () {
            Route::post('view_group', [GroupController::class, 'view_group']);
        });

        Route::group(['middleware' => ['is.delete_group']],function () {
            Route::post('delete_group', [GroupController::class, 'delete_group']);
        });

        Route::group(['middleware'=>['is.view_groups']],function(){
            Route::post('view_groups', [GroupController::class, 'view_groups']);

        });

        Route::group(['middleware' => ['is.edit_person']],function () {
            Route::post('add_image', [PersonController::class, 'add_image']);
            Route::post('update_person', [PersonController::class, 'update_person']);
            
        });

        Route::group(['middleware' => ['is.delete_person']],function () {
            Route::post('delete_person', [PersonController::class, 'delete_person']);
        });

        Route::group(['middleware' => ['is.appoint']],function () {
            Route::post('appoint', [PermissionController::class, 'appoint']);
            Route::post('edit_permission', [PermissionController::class, 'edit_permission']);
            Route::post('delete_permission', [PermissionController::class, 'delete_permission']);
          
        });

        Route::group(['middleware' => ['is.appoint_student']],function () {
            Route::post('appoint_student', [StudentController::class, 'appoint_student']);
            Route::post('edit_student', [StudentController::class, 'edit_student']);
        });
        Route::group(['middleware' => ['is.add_group']],function () {
            Route::post('create_group', [GroupController::class, 'create_group']);
        });

        Route::group(['middleware' => ['is.edit_group']],function () {
            Route::post('edit_group', [GroupController::class, 'edit_group']);
        });

        Route::group(['middleware' => ['is.reciter']],function () {
            Route::post('recite', [RecitingController::class, 'recite']);
            Route::post('edit_reciting', [RecitingController::class, 'edit_reciting']);
            Route::post('delete_reciting', [RecitingController::class, 'delete_reciting']);


        });

        Route::group(['middleware' => ['is.view_recite']],function () {
            Route::post('view_memorization', [RecitingController::class, 'view_memorization']);
            Route::post('view_recite', [RecitingController::class, 'view_recite']);
        });

        Route::group(['middleware' => ['is.tester']],function () {
            Route::post('test', [TestController::class, 'test']);
            Route::post('edit_test', [TestController::class, 'edit_test']);
            Route::post('view_test', [TestController::class, 'view_test']);
            Route::post('delete_test', [TestController::class, 'delete_test']);
            Route::post('get_students_for_testers', [StudentController::class, 'get_students_for_testers']);
            Route::post('tests_in_date_range',[TestController::class, 'tests_in_date_range']);
        });

        Route::group(['middleware' => ['is.attendance']],function () {
            Route::post('attendance', [AttendanceController::class, 'attendance']);


        });
        
        Route::group(['middleware' => ['is.view_attendance']],function () {
            Route::post('view_attendance', [AttendanceController::class, 'view_attendance']);
            Route::post('view_student_attendace', [AttendanceController::class, 'view_student_attendace']);


        });

        Route::group(['middleware' => ['is.evaluation']],function () {
            Route::post('add_additional_points', [Additional_PointsController::class, 'add_additional_points']);
            Route::post('edit_additional_points', [Additional_PointsController::class, 'edit_additional_points']);
            Route::post('delete_additional_points', [Additional_PointsController::class, 'delete_additional_points']);

            Route::post('view_additional_points', [Additional_PointsController::class, 'view_additional_points']);
        });

        Route::group(['middleware' => ['is.seller']],function () {
            Route::post('sell', [Additional_PointsController::class, 'sell']);
        });
    });


});