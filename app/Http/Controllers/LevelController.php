<?php

namespace App\Http\Controllers;

use App\Models\Level;

class LevelController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(Level::class, 'role_level', ['id', 'level_name']);
    }
}
