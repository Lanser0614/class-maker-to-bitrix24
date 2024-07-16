<?php
declare(strict_types=1);

namespace App\DTO;

class ClasMakerWebhookDTO
{

    private ?string $date = null;



    public function __construct(
        public readonly string $testName,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly float $percentage,
        public readonly string $passportNumber,
    )
    {
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): static
    {
        $this->date = $date;
        return $this;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['test']['test_name'],
            $data['result']['first'],
            $data['result']['last'],
            $data['result']['email'],
            $data['result']['percentage'],
            $data['result']['extra_info_answer'],
        );
    }

    public function getTestName(): string
    {
        return $this->testName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPercentage(): float
    {
        return $this->percentage;
    }

    public function getPassportNumber(): string
    {
        return $this->passportNumber;
    }


}
