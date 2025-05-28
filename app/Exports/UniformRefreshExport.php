<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\EmployeeDetail;
use App\Models\Inventory;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class uniformRefreshExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        $inventories = Inventory::with(['user.department', 'item'])
            ->whereHas('item', function ($query) {
                $query->where('type', 'baju');
            })
            ->where('acc_date', '<=', Carbon::now()->subMonths(12))
            ->where('status', 'Diterima')
            ->get();

        $data = $inventories->groupBy('user.id')->map(function ($userInventories) {
            $user = $userInventories->first()->user;
            return [
                'npk' => $user?->npk ?? 'N/A',
                'name' => $user?->fullname ?? 'N/A',
                'department' => $user?->department?->department_name ?? 'N/A',
                'inventories' => $userInventories->map(function ($inventory) {
                    return [
                        'item_name' => $inventory->item->name ?? 'N/A',
                        'acc_date' => $inventory->acc_date,
                        'status' => $inventory->status,
                    ];
                })->values(),
            ];
        })->values();

        dd($data);
        return $data;
    }


    public function headings(): array
    {
        return [
            'NPK',
            'Nama Lengkap',
            'Departemen',
            'Group',
            'Type',
            'Jenis Kelamin',
            'Tanggal Lahir',
        ];
    }
}
