<?php

use App\DTO\ClasMakerWebhookDTO;
use App\Enum\AcademicSubjectEnum;
use App\Services\Bitrix24\Bitrix24HttpRepository;
use App\UseCases\ClasMaker\SendExamResultToBitrixUseCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Enum;

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
});


Route::get('/csv', function (SendExamResultToBitrixUseCase $useCase) {

});
