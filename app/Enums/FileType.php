<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class FileType extends Enum
{
    const HEADER = 1;

    const DETAIL = 2;

    const FOOTER = 3;
}
