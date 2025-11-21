<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Carbon\Carbon;
use Exception;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class UniformRefreshController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $dateInput = request()->input('date'); // expecting format: YYYY-MM

            // Jika tidak ada input, gunakan bulan ini
            $inputDate = $dateInput ? Carbon::parse($dateInput) : Carbon::now();

            // Ambil tanggal 12 bulan sebelumnya dari input
            $annual = $inputDate->copy()->endOfMonth()->subMonths(12);

            $uniformRefresh = Inventory::with(['user', 'item', 'employeeJob.department'])
                ->whereHas('item', function ($query) {
                    $query->where('type', 'baju');
                })
                ->whereHas('employeeJob', function ($query) {
                    $query->where('employment_status', true);
                })
                ->where('acc_date', '<=', $annual)
                ->where('status', 'Diterima')
                ->get()
                ->map(function ($inventory) {
                    $user = $inventory->user;

                    return [
                        'id' => $user?->id,
                        'npk' => $user?->npk ?? 'N/A',
                        'name' => $user?->fullname ?? 'N/A',
                        'department' => $inventory->employeeJob->department->department_name ?? 'N/A',
                        'los' => $user?->LOS(),
                        'status' => $inventory->employeeJob->job_status ?? 'N/A',
                    ];
                })
                ->unique('id') // pakai 'id' karena kita kembalikan id user
                ->values();

            if (request()->has('export') && request('export') === 'excel') {
                $filename = 'uniform-refresh-' . Str::slug($inputDate->format('F-Y')) . '.xlsx';

                $export = $uniformRefresh->map(function ($item) {
                    return [
                        'NPK' => $item['npk'],
                        'Nama' => $item['name'],
                        'Departemen' => $item['department'],
                        'Status' => $item['status'],
                        'LOS' => $item['los'],
                    ];
                });                

                return Excel::download(new \App\Exports\EmployeeExport($export), $filename);
            }

            // dd($uniformRefresh);
            return view('admin.reporting.uniform', compact('uniformRefresh'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
