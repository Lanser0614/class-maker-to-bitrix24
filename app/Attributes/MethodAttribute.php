<?php
declare(strict_types=1);

namespace App\Attributes;

use App\Enum\AcademicSubjectEnum;
use Attribute;

#[Attribute]
class MethodAttribute
{
    public function __construct(public AcademicSubjectEnum $enum)
    {
    }

}
