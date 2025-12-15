<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ParticipantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/surveyor/surveys', [SurveyController::class, 'index'])
    ->middleware(['auth', 'role:surveyor']);

Route::get('/surveyor/survey/create', [SurveyController::class, 'create'])
    ->middleware(['auth', 'role:surveyor']);

Route::post('/surveyor/survey/store', [SurveyController::class, 'store'])
    ->middleware(['auth', 'role:surveyor']);

Route::get('/participant/dashboard', [ParticipantController::class, 'dashboard'])
    ->middleware(['auth', 'role:participant']);

Route::get('/survey/{id}', [SurveyController::class, 'show'])
    ->middleware('auth');

Route::post('/survey/{id}/submit', [ParticipantController::class, 'submit'])
    ->middleware('auth');

Route::get('/dashboard', function () {
    if (auth()->user()->role == 'surveyor') {
        return redirect('/surveyor/surveys');
    }
    return redirect('/participant/dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/surveyor/survey/{id}/responses', [SurveyController::class, 'responses'])
    ->middleware(['auth','role:surveyor']);



require __DIR__ . '/auth.php';
