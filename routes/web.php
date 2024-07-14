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
    $enum = AcademicSubjectEnum::from(AcademicSubjectEnum::getValueByKey('Biology'));


    dd($enum);
});


Route::get('/csv', function (SendExamResultToBitrixUseCase $useCase) {

    $file = fopen(storage_path('/app/data.csv'), 'r');

    $data = [];
    while(! feof($file))
    {
       $data[] = fgetcsv($file);
    }
    fclose($file);

    foreach($data as $key => $d) {
        if ($key >= 7
            and isset($d[0])
            and isset($d[1])
            and isset($d[2])
            and isset($d[4])
            and isset($d[5])
        ) {
            try {
                $newData[] = [
                    'name' => $d[0],
                    'last_name' => $d[1],
                    'email' => $d[2],
                    'subject' => $d[4],
                    'percentage' => $d[5],
                    'date' => $d[11],
                    'passport' => $d[17],
                ];
            } catch (Exception $e) {
                dd($d);
            }
        }
    }

    $collection = collect($newData);

    $items = $collection->skip(10)->take(1);

    $items->map(callback: function ($item) use ($useCase) {
        $dto = new ClasMakerWebhookDTO(
            testName: $item['subject'],
            firstName: $item['name'],
            lastName: $item['last_name'],
            email: $item['email'],
            percentage: (int) $item['percentage'],
            passportNumber: $item['passport']
        );

        $dto->setDate($item['date']);

        $result = $useCase->execute($dto);

        dump($result);

    });

    dd('ok');

});

function getMethodAttributes(AcademicSubjectEnum $enum) {
    $reflectionClass = new ReflectionClass(Bitrix24HttpRepository::class);
    $methods = $reflectionClass->getMethods();

    foreach ($methods as $method) {
        $attributes = $method->getAttributes();
        if (count($attributes) > 0) {
           return $method;
        }
    }
}
