<?php
declare(strict_types=1);

namespace App\Services\Bitrix24\Response;

class BitrixResponseDTO
{
    public function __construct(
        public readonly int $total,
        public readonly array $result,
    )
    {
    }


    public static function fromArray(array $data): self
    {
        $result = (count($data['result']) === 0) ? [] : array_shift($data['result']);

        return new self(
            $data['total'],
            $result,
        );
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getResult(): array
    {
        return $this->result;
    }



}
