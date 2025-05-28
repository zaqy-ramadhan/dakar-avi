<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends UniversalCrudController
{
    public function __construct()
    {
        parent::__construct(Item::class, 'dakar_items', ['id', 'item_name']);
    }
}
