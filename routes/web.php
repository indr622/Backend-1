<?php

use App\Helpers\LogHelper;
use App\Models\User;
use App\Repositories\ExampleRepository;
use App\Services\ExampleService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

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
    return response()->json([
        'message' => 'Welcome to API Waizly',
        'status' => 'Connected',
        'version' => '1.0.0',
        'by' => 'Indra Basuki'
    ]);
});
