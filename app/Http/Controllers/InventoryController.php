<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:dakar_items,id',
            'sizes' => 'required|array',
            'sizes.*' => 'nullable|string',
            'status_diterima' => 'nullable|array',
            'status_diterima.*' => 'boolean',
            'status_dikembalikan' => 'nullable|array',
            'status_dikembalikan.*' => 'boolean',
        ]);

        $user = User::findOrFail($id);

        $statusDiterima = $request->status_diterima ?? [];
        $statusDikembalikan = $request->status_dikembalikan ?? [];

        foreach ($request->items as $index => $itemId) {
            $currentStatusDiterima = isset($statusDiterima[$index]) ? 'Diterima' : null;
            $currentStatusDikembalikan = isset($statusDikembalikan[$index]) ? 'Dikembalikan' : null;

            $item = Item::findOrFail($itemId);

            $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day'];
            $itemName = $item->item_name;
            // dd(strpos($itemName, 'Seragam Biru'));

            if ($request->sizes[$index] === "-") {
                if (strpos($itemName, 'Seragam ESD') !== false) {
                    $size = $user->employeeDetail->esd_uniform_size ?? 'Default Size';
                } elseif (strpos($itemName, 'Sepatu ESD') !== false) {
                    $size = $user->employeeDetail->esd_shoes_size ?? 'Default Size';
                } elseif (strpos($itemName, 'Seragam Biru') !== false) {
                    $size = $user->employeeDetail->blue_uniform_size ?? 'Default Size';
                } elseif (strpos($itemName, 'Kaos Polo') !== false) {
                    $size = $user->employeeDetail->polo_shirt_size ?? 'Default Size';
                } elseif (strpos($itemName, 'Sepatu Safety') !== false) {
                    $size = $user->employeeDetail->safety_shoes_size ?? 'Default Size';
                } else {
                    $size = $request->sizes[$index] ?? '-';
                }
                // dd($size);
            } else {
                $size = $request->sizes[$index];
            }
            if (in_array(strtolower($item->item_name), $specificItems)) {
                $startDate = optional($user->employeeJob)->last()->start_date ?? null;

                if ($startDate) {
                    $dueDate = Carbon::parse($startDate)->addMonth();
                } else {
                    $dueDate = null;
                }
            } else {
                $dueDate = optional($user->employeeJob)->last()->start_date ?? null;
            }

            Inventory::create([
                'user_id' => $user->id,
                'item_id' => $itemId,
                'due_date' => $dueDate,
                'acc_date' => $currentStatusDiterima === 'Diterima' ? Carbon::now() : null,
                'return_date' => $currentStatusDikembalikan === 'Dikembalikan' ? Carbon::now() : null,
                'employee_job_id' => optional($user->employeeJob)->last()->id ?? null,
                // 'size' => $request->sizes[$index] ?? null,
                'size' => $size,
                'status' => $currentStatusDiterima ?? $currentStatusDikembalikan ?? '-',
            ]);
        }

        return redirect()->back()->with('success', 'Inventaris berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        // dd($request);

        $request->validate([
            'items' => 'nullable|array',
            'items.*' => 'exists:dakar_items,id',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string',
            'employee_job_ids' => 'nullable|array',
            'employee_job_ids.*' => 'nullable',
            'status' => 'nullable|array',
            'status.*' => 'nullable|in:Diterima,Dikembalikan,-',
            'return_notes' => 'nullable|array',
            'return_notes.*' => 'nullable|string',
        ]);

    
        $user = User::findOrFail($id);
    
        // Hapus inventory yang tidak lagi diperlukan
        Inventory::where('user_id', $user->id)->delete();
    
        $status = $request->status ?? [];
    
        if ($request->items !== null) {
            foreach ($request->items as $index => $itemId) {
                $item = Item::findOrFail($itemId);
                $itemName = $item->item_name;
    
                $size = $request->sizes[$index] ?? '-';
    
                if ($size === "-") {
                    if (strpos($itemName, 'Seragam ESD') !== false) {
                        $size = $user->employeeDetail->esd_uniform_size ?? 'Default Size';
                    } elseif (strpos($itemName, 'Sepatu ESD') !== false) {
                        $size = $user->employeeDetail->esd_shoes_size ?? 'Default Size';
                    } elseif (strpos($itemName, 'Seragam Biru') !== false) {
                        $size = $user->employeeDetail->blue_uniform_size ?? 'Default Size';
                    } elseif (strpos($itemName, 'Kaos Polo') !== false) {
                        $size = $user->employeeDetail->polo_shirt_size ?? 'Default Size';
                    } elseif (strpos($itemName, 'Sepatu Safety') !== false) {
                        $size = $user->employeeDetail->safety_shoes_size ?? 'Default Size';
                    } else {
                        $size = $request->sizes[$index] ?? '-';
                    }
                }
    
                $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day', 'user account e-slip'];
    
                $dueDate = null;
                if (in_array(strtolower($item->item_name), $specificItems)) {
                    $startDate = optional($user->employeeJob)->last()->start_date ?? null;
    
                    if ($startDate) {
                        $dueDate = Carbon::parse($startDate)->addMonth();
                    }
                } else {
                    $dueDate = $request->due_date[$index] ?? Carbon::now()->addDays(3);
                }
    
                $employeeJobId = $request->employee_job_ids[$index];
                if ($employeeJobId === 'null') {
                    $employeeJobId = optional($user->employeeJob)->last()->id;
                }
    
                $currentStatus = $status[$index] ?? '-';
                $returnNotes = $request->return_notes[$index] ?? null;
    
                Inventory::create([
                    'user_id' => $user->id,
                    'item_id' => $itemId,
                    'due_date' => $dueDate,
                    'acc_date' => !empty($request->acc_date[$index])
                        ? $request->acc_date[$index]
                        : ($currentStatus === 'Diterima' ? Carbon::now() : null),
                    'return_date' => !empty($request->return_date[$index])
                        ? $request->return_date[$index]
                        : ($currentStatus === 'Dikembalikan' ? Carbon::now() : null),
                    'employee_job_id' => $employeeJobId,
                    'size' => $size,
                    'status' => $currentStatus,
                    'return_notes' => $returnNotes,
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Inventaris berhasil diperbarui.');
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'items' => 'nullable|array',
    //         'items.*' => 'exists:dakar_items,id',
    //         'sizes' => 'nullable|array',
    //         'sizes.*' => 'nullable|string',
    //         'employee_job_ids' => 'nullable|array',
    //         'employee_job_ids.*' => 'nullable',
    //         'status' => 'nullable|array',
    //         'status.*' => 'in:Diterima,Dikembalikan,-'
    //     ]);
    //     // dd($request);

    //     $user = User::findOrFail($id);

    //     Inventory::where('user_id', $user->id)->delete();

    //     $status = $request->status ?? [];

    //     if ($request->items !== null) {
    //         foreach ($request->items as $index => $itemId) {
    //             $item = Item::findOrFail($itemId);
    //             $itemName = $item->item_name;

    //             if ($request->sizes[$index] === "-") {
    //                 if (strpos($itemName, 'Seragam ESD') !== false) {
    //                     $size = $user->employeeDetail->esd_uniform_size ?? 'Default Size';
    //                 } elseif (strpos($itemName, 'Sepatu ESD') !== false) {
    //                     $size = $user->employeeDetail->esd_shoes_size ?? 'Default Size';
    //                 } elseif (strpos($itemName, 'Seragam Biru') !== false) {
    //                     $size = $user->employeeDetail->blue_uniform_size ?? 'Default Size';
    //                 } elseif (strpos($itemName, 'Kaos Polo') !== false) {
    //                     $size = $user->employeeDetail->polo_shirt_size ?? 'Default Size';
    //                 } elseif (strpos($itemName, 'Sepatu Safety') !== false) {
    //                     $size = $user->employeeDetail->safety_shoes_size ?? 'Default Size';
    //                 } else {
    //                     $size = $request->sizes[$index] ?? '-';
    //                 }
    //                 // dd($size);
    //             } else {
    //                 $size = $request->sizes[$index];
    //             }

    //             $specificItems = ['bpjs kesehatan', 'bpjs tk', 'user account great day'];

    //             if (in_array(strtolower($item->item_name), $specificItems)) {
    //                 $startDate = optional($user->employeeJob)->last()->start_date ?? null;

    //                 if ($startDate) {
    //                     $dueDate = Carbon::parse($startDate)->addMonth();
    //                 } else {
    //                     $dueDate = null;
    //                 }
    //             } else {
    //                 $dueDate = $request->due_date[$index] ?? Carbon::now()->addDays(3);
    //             }

    //             $employeeJobId = $request->employee_job_ids[$index];
    //             if ($employeeJobId === 'null') {
    //                 $employeeJobId = optional($user->employeeJob)->last()->id;
    //             }
    //             $currentStatus = $status[$index] ?? '-';

    //             Inventory::create([
    //                 'user_id' => $user->id,
    //                 'item_id' => $itemId,
    //                 'due_date' => $dueDate,
    //                 'acc_date' => !empty($request->acc_date[$index])
    //                     ? $request->acc_date[$index]
    //                     : ($currentStatus === 'Diterima' ? Carbon::now() : null),

    //                 'return_date' => !empty($request->return_date[$index])
    //                     ? $request->return_date[$index]
    //                     : ($currentStatus === 'Dikembalikan' ? Carbon::now() : null),
    //                 'employee_job_id' => $employeeJobId,
    //                 // 'size' => $request->sizes[$index] ?? null,
    //                 'size' => $size,
    //                 'status' => $request->status[$index] ?? $currentStatus,
    //             ]);
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Inventaris berhasil diperbarui.');
    // }
}
