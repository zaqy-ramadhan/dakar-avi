<?php

namespace App\Http\Controllers;

use App\Models\Golongan;

class GolonganController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(Golongan::class, 'dakar_golongan', ['id', 'golongan_name']);
    }
}
