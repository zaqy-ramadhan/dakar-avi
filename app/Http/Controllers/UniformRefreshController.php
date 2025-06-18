<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Carbon\Carbon;
use Exception;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UniformRefreshController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $now = Carbon::now();
            // Terima input bulan dan tahun secara dinamis dari request
            $bulan = request()->input('month');
            $tahun = request()->input('month');

            // Jika input tidak ada, gunakan bulan dan tahun saat ini
            if (!$bulan || !$tahun) {
                $bulan = $now->month;
                $tahun = $now->year;
            }

            // dd($bulan);

            // Buat objek Carbon dari input bulan dan tahun
            $inputDate = Carbon::createFromDate($tahun, $bulan);

            // Ambil tanggal 12 bulan sebelumnya dari input
            $now = $inputDate->subMonths(12);

            $uniformRefresh = Inventory::with(['user', 'item', 'employeeJob.department'])
                ->whereHas('item', function ( $query) {
                    $query->where('type', 'baju');
                })
                ->whereHas('employeeJob', function ($query) {
                    $query->where('employment_status', true);
                })
                ->where('acc_date', '<=',  $now)
                ->where('status', 'Diterima')
                ->get()
                ->map(function ($inventory) {
                    $user = $inventory->user;

                    return [
                        'id' => $inventory->user->id,
                        'npk' => $user?->npk ?? 'N/A',
                        'name' => $user?->fullname ?? 'N/A',
                        'department' => $inventory->employeeJob->department->department_name ?? 'N/A',
                    ];
                })
                ->unique('user_id')
                ->values();
                dd($uniformRefresh);
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
