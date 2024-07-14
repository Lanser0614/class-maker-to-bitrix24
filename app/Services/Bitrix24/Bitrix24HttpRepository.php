<?php
declare(strict_types=1);

namespace App\Services\Bitrix24;

use App\Attributes\MethodAttribute;
use App\DTO\ClasMakerWebhookDTO;
use App\Enum\AcademicSubjectEnum;
use App\Services\Bitrix24\Response\BitrixResponseDTO;
use App\Services\Bitrix24\Response\BitrixResponseOnCreateEntityDTO;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Bitrix24HttpRepository
{
    private function baseRequest(string $method, array $params = []): Response
    {
        return Http::timeout(20)
            ->post(config('services.bitrix24.rest_url') . $method, $params);
    }

    public function getDealByPassportNumber(string $passportNumber): BitrixResponseDTO
    {
        $deal = $this->baseRequest('crm.deal.list', [
            'filter' => [
                'UF_CRM_667F1708E08A3' => $passportNumber
            ],
            'select' => array_merge( AcademicSubjectEnum::getValues(), ['ID'])
        ])->json();

        return BitrixResponseDTO::fromArray($deal);
    }

    public function getContactByPassportNumber(string $passportNumber): BitrixResponseDTO
    {
        $deal = $this->baseRequest('crm.contact.list', [
            'filter' => [
                'UF_CRM_667C0B84BCD20' => $passportNumber
            ],
        ])->json();

        return BitrixResponseDTO::fromArray($deal);
    }

    #[MethodAttribute(AcademicSubjectEnum::Biology)]
    public function updateDealTestResultBiology(string $dealId, int $percentage)
    {
        return $this->baseRequest('crm.deal.update', [
            'ID' => $dealId,
            'fields' => [AcademicSubjectEnum::Biology->value => $percentage]
        ])->json();
    }

    #[MethodAttribute(AcademicSubjectEnum::Chemistry)]
    public function updateDealTestResultChemistry(string $dealId, int $percentage): Response
    {
        return $this->baseRequest('crm.deal.update', [
            'ID' => $dealId,
            'fields' => [AcademicSubjectEnum::Chemistry->value => $percentage]
        ]);
    }

    #[MethodAttribute(AcademicSubjectEnum::English)]
    public function updateDealTestResultEnglish(string $dealId, int $percentage): Response
    {
        return $this->baseRequest('crm.deal.update', [
            'ID' => $dealId,
            'fields' => [AcademicSubjectEnum::English->value => $percentage]
        ]);
    }

    #[MethodAttribute(AcademicSubjectEnum::Mathematics)]
    public function updateDealTestResultMath(string $dealId, int $percentage): Response
    {
        return $this->baseRequest('crm.deal.update', [
            'ID' => $dealId,
            'fields' => [AcademicSubjectEnum::Mathematics->value => $percentage]
        ]);
    }

    public function storeContact(ClasMakerWebhookDTO $webhookDTO): BitrixResponseOnCreateEntityDTO
    {
        $response = $this->baseRequest('crm.contact.add', [
            'fields' => [
                'NAME' => $webhookDTO->getFirstName(),
                'LAST_NAME' => $webhookDTO->getLastName(),
                'EMAIL' => [
                    ['VALUE' => $webhookDTO->getEmail()]
                ],
                'UF_CRM_667C0B84BCD20' => $webhookDTO->getPassportNumber()
            ]
        ])->json();

        return BitrixResponseOnCreateEntityDTO::fromArray($response);
    }

    public function storeDeal(string $contactId, ClasMakerWebhookDTO $webhookDTO)
    {
        $response = $this->baseRequest('crm.deal.add', [
            'fields' => [
                'TITLE' => 'classroom ' . $webhookDTO->getFirstName(),
                'UF_CRM_667F1708E08A3' => $webhookDTO->getPassportNumber(),
                'CONTACT_ID' => $contactId,
            ]
        ])->json();

        return BitrixResponseOnCreateEntityDTO::fromArray($response);
    }

    public function addCommentToTimeLineToDeal(string $dealId, AcademicSubjectEnum $enum,  int $percentage): Response
    {
        return $this->baseRequest("crm.timeline.comment.add", [
            'fields' => [
                'ENTITY_ID' => $dealId,
                'ENTITY_TYPE' => 'deal',
                'COMMENT' => $enum->name . ': ' . $percentage,
            ]
        ]);
    }

    public function updateDealDate(string $dealId, string $date): Response
    {
        return $this->baseRequest('crm.deal.update', [
            'ID' => $dealId,
            'fields' => ['UF_CRM_1719688190448' => $date]
        ]);
    }

}
