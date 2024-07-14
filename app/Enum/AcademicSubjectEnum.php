<?php

namespace App\Enum;

enum AcademicSubjectEnum: string
{
    case Biology = 'UF_CRM_1719688035688';
    case Chemistry = 'UF_CRM_1719688069350';
    case English = 'UF_CRM_1719688106186';
    case Mathematics  = 'UF_CRM_1719688123666';


    public static function checkKey(string $key): bool
    {
        $subjects = array_column(AcademicSubjectEnum::cases(), 'name');
        return in_array($key, $subjects);
    }

    public static function getValueByKey(string $key)
    {
        foreach (AcademicSubjectEnum::cases() as $enumKey => $value) {
            if ($value->name === $key) {
                return $value->value;
            }
        }
    }

    public static function getValues(): array
    {
        return array_column(AcademicSubjectEnum::cases(), 'value');
    }
}
