<?php
declare(strict_types=1);

namespace App\UseCases\ClasMaker;

use App\DTO\ClasMakerWebhookDTO;
use App\Enum\AcademicSubjectEnum;
use App\Services\Bitrix24\Bitrix24HttpRepository;
use Exception;
use ReflectionClass;

class SendExamResultToBitrixUseCase
{

    public function __construct(
        private readonly Bitrix24HttpRepository $httpRepository
    )
    {
    }

    /**
     * @param ClasMakerWebhookDTO $requestDto
     * @return mixed
     * @throws Exception
     */
    public function execute(ClasMakerWebhookDTO $requestDto): mixed
    {
        $deal = $this->httpRepository->getDealByPassportNumber($requestDto->getPassportNumber());

        if ($deal->getTotal() === 0) {
            $contact = $this->httpRepository->getContactByPassportNumber($requestDto->getPassportNumber());

            try {
                if ($contact->getTotal() === 0) {
                    $storeContactResponse = $this->httpRepository->storeContact($requestDto);
                    $this->httpRepository->storeDeal($storeContactResponse->getId(), $requestDto);
                } else {
                    $this->httpRepository->storeDeal($contact->getResult()['ID'], $requestDto);
                }
            } catch (Exception|\TypeError $exception) {
                return $exception->getMessage();
            }


            $deal = $this->httpRepository->getDealByPassportNumber($requestDto->getPassportNumber());
        }


        $subjectName = $this->getSubjectName($requestDto->getTestName());

        $enum = AcademicSubjectEnum::from(AcademicSubjectEnum::getValueByKey($subjectName));

        try {
            $oldResult = (int)$deal->getResult()[$enum->value];
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($requestDto->getPercentage() > $oldResult) {
            $method = $this->getMethodAttributes($enum);

            $this->httpRepository->$method($deal->getResult()['ID'], $requestDto->getPercentage());
        }

        $dealId = $deal->getResult()['ID'];
        $this->httpRepository->updateDealDate($dealId, $requestDto->getDate());
        $this->httpRepository->addCommentToTimeLineToDeal($dealId, $enum, $requestDto->getPercentage());

        return $dealId;
    }


    private function getMethodAttributes(AcademicSubjectEnum $enum): string
    {
        $reflectionClass = new ReflectionClass(Bitrix24HttpRepository::class);
        $methods = $reflectionClass->getMethods();

        foreach ($methods as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                $arrayAttribute = $attribute->getArguments();
                /** @var AcademicSubjectEnum $methodEnum */
                $methodEnum = array_shift($arrayAttribute);
                if ($methodEnum->name === $enum->name) {
                    return $method->getName();
                }
            }
        }

        throw new Exception("Undefined method");
    }

    /**
     * @param string $testName
     * @return string
     * @throws Exception
     */
    private function getSubjectName(string $testName): string
    {
        $explodeWords = explode(" ", $testName);

        foreach ($explodeWords as $word) {
            if (AcademicSubjectEnum::checkKey($word)) {
                return $word;
            }
        }

        throw new Exception("Unknown subject name");
    }
}
