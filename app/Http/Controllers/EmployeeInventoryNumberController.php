<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeInventoryNumber;
use App\Models\Item;
use App\Models\User;

class EmployeeInventoryNumberController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'bpjs' => 'nullable|string',
            'bpjstk' => 'nullable|string',
            'username_eslip' => 'nullable|string',
            'password_eslip' => 'nullable|string',
            'username_greatday' => 'nullable|string',
            'password_greatday' => 'nullable|string',
        ]);
    
        $user = User::findOrFail($validatedData['user_id']);
    
        $bpjs = Item::where('item_name', 'BPJS Kesehatan')->value('id');
        $bpjstk = Item::where('item_name', 'BPJS TK')->value('id');
        $greatday = Item::where('item_name', 'User Account Great Day')->value('id');
        $eslip = Item::where('item_name', 'User Account E-Slip')->value('id');
        $pass_greatday = Item::where('item_name', 'User Password Great Day')->value('id');
        $pass_eslip = Item::where('item_name', 'User Password E-Slip')->value('id');
    
        $items = [
            ['item_id' => $bpjs, 'number' => $validatedData['bpjs'] ?? null],
            ['item_id' => $bpjstk, 'number' => $validatedData['bpjstk'] ?? null],
            ['item_id' => $eslip, 'number' => $validatedData['username_eslip'] ?? null],
            ['item_id' => $pass_eslip, 'number' => $validatedData['password_eslip'] ?? null],
            ['item_id' => $greatday, 'number' => $validatedData['username_greatday'] ?? null],
            ['item_id' => $pass_greatday, 'number' => $validatedData['password_greatday'] ?? null],
        ];
    
        foreach ($items as $item) {
            if ($item['number']) {
                EmployeeInventoryNumber::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'item_id' => $item['item_id'],
                    ],
                    [
                        'number' => $item['number'],
                        'updated_at' => now(),
                    ]
                );
            }
        }
    
        return redirect()->back()->with('success', 'Inventory numbers saved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmployeeInventoryNumber  $inventoryNumber
     * @return \Illuminate\Http\Response
     */
}