<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(Group::class, 'dakar_group', ['id', 'group_name']);
    }
}
