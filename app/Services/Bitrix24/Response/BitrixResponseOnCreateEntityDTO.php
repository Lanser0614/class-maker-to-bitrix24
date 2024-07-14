<?php
declare(strict_types=1);

namespace App\Services\Bitrix24\Response;

class BitrixResponseOnCreateEntityDTO
{
    public function __construct(
        public readonly string $id,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public static function fromArray(array $data): self
    {
        $id = (string)$data['result'];
        return new self($id);
    }
}
