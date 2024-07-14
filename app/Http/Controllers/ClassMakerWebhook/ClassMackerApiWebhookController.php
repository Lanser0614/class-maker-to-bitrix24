<?php
declare(strict_types=1);

namespace App\Http\Controllers\ClassMakerWebhook;

use App\DTO\ClasMakerWebhookDTO;
use App\Enum\AcademicSubjectEnum;
use App\Http\Request\ClassMackerWebhookRequest;
use App\Services\Bitrix24\Bitrix24HttpRepository;
use App\UseCases\ClasMaker\SendExamResultToBitrixUseCase;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use ReflectionClass;
use ReflectionMethod;

class ClassMackerApiWebhookController
{
    public function __construct(
        private Bitrix24HttpRepository $httpRepository
    )
    {
    }

    /**
     * @param ClassMackerWebhookRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function webHook(ClassMackerWebhookRequest $request, SendExamResultToBitrixUseCase $useCase): JsonResponse
    {
        Log::info('test', $request->all());
        if ($request->input('payload_status') === 'verify') {
            Log::info('test', $request->all());
            return new JsonResponse(['data' => "test"], 200);
        }

        $requestDto = ClasMakerWebhookDTO::fromArray($request->validated());

        $dealId = $useCase->execute($requestDto);

        return new JsonResponse([
            'data' => $dealId,
        ], 200);
    }





}
