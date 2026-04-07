<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;

class CostCenterController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(CostCenter::class, 'dakar_cost_centers', ['id', 'cost_center_name']);
    }
}
