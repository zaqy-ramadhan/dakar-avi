<?php

namespace App\Http\Controllers;

use App\Models\SubGolongan;

class SubGolonganController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(SubGolongan::class, 'dakar_sub_golongan', ['id', 'sub_golongan_name']);
    }
}
