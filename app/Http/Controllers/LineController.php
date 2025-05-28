<?php

namespace App\Http\Controllers;

use App\Models\Line;

class LineController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(Line::class, 'dakar_line', ['id', 'line_name']);
    }
}
