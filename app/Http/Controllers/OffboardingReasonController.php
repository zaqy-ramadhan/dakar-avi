<?php

namespace App\Http\Controllers;

use App\Models\OffboardingReason;

class OffboardingReasonController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(OffboardingReason::class, 'dakar_offboarding_reason', ['id', 'reason']);
    }
}
