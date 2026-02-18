<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

        $log = ActivityLog::create([
            'actor_id' => Auth::user()->id,
            'employee_id' => $user->id,
            'note' => 'Filling Starter Kit',
            'table_name' => 'users',
            'table_id' => $user->id,

        ]);

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

        $log = ActivityLog::create([
            'actor_id' => Auth::user()->id,
            'employee_id' => $user->id,
            'note' => 'Updating Starter Kit',
            'table_name' => 'users',
            'table_id' => $user->id,

        ]);
    
        return redirect()->back()->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function exportView()
    {   
        $allUsers = User::whereHas('latestEmployeeJob', function($q){
            $q->where('employment_status', true);
        })->get();
        return view('admin.users.exportInventory', compact('allUsers'));
    }

    public function exportKit(Request $request)
    {
        $query = User::with(['inventory.item', 'latestEmployeeJob.department']);
        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }
        $users = $query->get();

        return Excel::download(new class($users) implements WithEvents {
            private $users;

            public function __construct($users) {
                $this->users = $users;
            }

            public function registerEvents(): array {
                return [
                    AfterSheet::class => function(AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();
                        $currentRow = 1;

                        foreach ($this->users as $user) {
                            $sheet->setCellValue("A{$currentRow}", "Nama");
                            $sheet->setCellValue("B{$currentRow}", ": " . $user->fullname);
                            $currentRow++;

                            $sheet->setCellValue("A{$currentRow}", "NPK");
                            $sheet->setCellValue("B{$currentRow}", ": " . $user->npk);
                            $currentRow++;

                            $sheet->setCellValue("A{$currentRow}", "Department");
                            $sheet->setCellValue("B{$currentRow}", ": " . ($user->latestEmployeeJob?->department?->department_name ?? '-'));
                            $currentRow += 2; 

                            $tableHeaderRow = $currentRow;
                            $headers = ['No', 'Item Name', 'Size', 'Status', 'Acc Date', 'Return Date', 'Return Notes'];
                            foreach ($headers as $key => $title) {
                                $col = chr(65 + $key); 
                                $sheet->setCellValue("{$col}{$tableHeaderRow}", $title);
                            }
                            
                            $sheet->getStyle("A{$tableHeaderRow}:G{$tableHeaderRow}")->applyFromArray([
                                'font' => ['bold' => true],
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                            ]);
                            $currentRow++;

                            $startDataRow = $currentRow;
                            $no = 1;
                            foreach ($user->inventory as $item) {
                                $sheet->setCellValue("A{$currentRow}", $no++);
                                $sheet->setCellValue("B{$currentRow}", $item->item->item_name ?? '-');
                                $sheet->setCellValue("C{$currentRow}", $item->size);
                                $sheet->setCellValue("D{$currentRow}", $item->status);
                                $sheet->setCellValue("E{$currentRow}", $item->acc_date);
                                $sheet->setCellValue("F{$currentRow}", $item->return_date);
                                $sheet->setCellValue("G{$currentRow}", $item->return_notes);
                                $currentRow++;
                            }

                            $lastDataRow = ($no > 1) ? $currentRow - 1 : $currentRow;
                            if($no == 1) $currentRow++; 

                            $sheet->getStyle("A{$tableHeaderRow}:G{$lastDataRow}")->applyFromArray([
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                            ]);

                            $currentRow += 3; 
                        }

                        foreach (range('A', 'G') as $col) {
                            $sheet->getColumnDimension($col)->setAutoSize(true);
                        }
                    },
                ];
            }
        }, 'Starter_Kit_Report' . now()->format('Ymd') . '.xlsx');
    }

    // public function exportKit(Request $request)
    // {
    //     $query = User::with(['inventory.item', 'latestEmployeeJob.department']);
    //     if ($request->filled('id')) {
    //         $query->where('id', $request->input('id'));
    //     }
    //     $users = $query->get();

    //     // dd($users);

    //     $exportData = collect();
    //     $boundaries = [];
    //     $currentRow = 2; 

    //     foreach ($users as $user) {
    //         foreach ($user->inventory as $item) {
    //             $exportData->push([
    //                 'NPK'           => $user->npk,
    //                 'NAME'          => $user->fullname,
    //                 'DEPARTMENT'    => $user->latestEmployeeJob?->department?->department_name ?? '-',
    //                 'ITEM'          => $item->item->item_name ?? '-',
    //                 'SIZE'          => $item->size,
    //                 'STATUS'        => $item->status,
    //                 //'DUE_DATE'      => $item->due_date,
    //                 'ACC_DATE'      => $item->acc_date,
    //                 'RETURN_DATE'   => $item->return_date,
    //                 'RETURN_NOTES'  => $item->return_notes,
    //             ]);
    //             $currentRow++;
    //         }
    //         if ($user->inventory->count() > 0) {
    //             $boundaries[] = $currentRow - 1;
    //         }
    //     }

    //     return Excel::download(new class($exportData, $boundaries) implements FromCollection, WithHeadings, WithStyles {
    //         private $data;
    //         private $boundaries;

    //         public function __construct($data, $boundaries) {
    //             $this->data = $data;
    //             $this->boundaries = $boundaries;
    //         }

    //         public function collection() {
    //             return $this->data;
    //         }

    //         public function headings(): array {
    //             return ['NPK', 'Nama Karyawan', 'Departemen', 'Nama Barang', 'Size', 'Status', 'Due Date', 'Acc Date'];
    //         }

    //         public function styles(Worksheet $sheet) {
    //             $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    //             $sheet->getStyle('A1:H1')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

    //             foreach ($this->boundaries as $rowNum) {
    //                 $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getBorders()->getBottom()->applyFromArray([
    //                     'borderStyle' => Border::BORDER_THICK,
    //                     'color' => ['rgb' => '000000'],
    //                 ]);
    //             }

    //             foreach (range('A', 'H') as $columnID) {
    //                 $sheet->getColumnDimension($columnID)->setAutoSize(true);
    //             }
    //         }
    //     }, 'Inventory_Karyawan_' . now()->format('Ymd') . '.xlsx');
    // }

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
