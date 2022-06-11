<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BilletController;
use App\Http\Controllers\Api\DocController;
use App\Http\Controllers\Api\FoundAndLostController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WallController;
use App\Http\Controllers\Api\WarningController;

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    //Mural de Avisos
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'like']);

    //Documentos
    Route::get('/docs', [DocController::class, 'getAll']);

    //Livro de ocorrências
    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning', [WarningController::class, 'setWarning']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);

    //Boletos
    Route::get('/billets', [BilletController::class, 'getAll']);

    //Achados e Perdidos
    Route::get('/found-and-lost', [FoundAndLostController::class, 'getAll']);
    Route::post('/found-and-lost', [FoundAndLostController::class, 'insert']);
    Route::put('/found-and-lost/{id}', [FoundAndLostController::class, 'update']);

    //Unidade
    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/add-person', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/add-vehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/add-pet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/remove-person', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/remove-vehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/remove-pet', [UnitController::class, 'removePet']);

    //Reservas
    Route::get('/reservations', [ReservationController::class, 'getReservations']);
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);

    Route::get('/reservation/{id}/disabled-dates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservation/{id}/times', [ReservationController::class, 'getTimes']);

    Route::get('/my-reservations', [ReservationController::class, 'getMyReservations']);
    Route::delete('/my-reservation/{id}', [ReservationController::class, 'delMyReservations']);
});
