<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaxCalculateType extends Enum
{
    const ROUND_UP = '繰り上げ';

    const ROUND_OFF = '四捨五入';

    const ROUND_DOWN = '繰り下げ';
}
