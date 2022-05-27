<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaxType extends Enum
{
    const EXTERNAL = '外税';

    const INTERNAL = '内税';

    const NONE = 'なし';
}
