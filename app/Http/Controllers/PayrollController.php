<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\HolidayDate;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date_from = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $date_to = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        // Query payroll utama
        $query = Payroll::withCount('payrollDetail')
            ->withSum('payrollDetail', 'total_salary')
            ->when($request->date_from && $request->date_to, function ($q) use ($date_from, $date_to) {
                $q->whereBetween('start_date', [$date_from, $date_to]);
            })
            ->when($request->department, function ($q, $val) {
                $q->whereHas('payrollDetail.user.employeeJob.department', function ($qq) use ($val) {
                    $qq->where('department_name', $val);
                });
            });

        $payrolls = $query->get();
        // dd($payrolls);

        // Jika permintaan AJAX -> untuk DataTables
        if ($request->ajax()) {
            return DataTables::of($payrolls)
                ->addIndexColumn()
                ->addColumn('periode', function ($p) {
                    return Carbon::parse($p->start_date)->isoFormat('D MMM Y') . ' - ' .
                        Carbon::parse($p->end_date)->isoFormat('D MMM Y');
                })
                ->addColumn('total_employee', fn($p) => $p->payroll_detail_count)
                ->addColumn('total_salary', fn($p) => 'Rp ' . number_format($p->total_salary, 0, ',', '.'))
                ->addColumn('action', function ($p) {
                    $editButton = '<a href="' . route('payroll.edit', $p->id) . '" class="btn btn-sm btn-outline-warning me-1" title="Edit Data"><i class="ti ti-edit fs-4"></i></a>';
                    $exportButton = '<a href="' . route('payroll.export', $p->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Export Data"><i class="ti ti-file-spreadsheet fs-4"></i></a>';
                    $deleteButton = '
                        <form action="' . route('payroll.destroy', $p->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this payroll data?\');" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Data"><i class="ti ti-trash fs-4"></i></button>
                        </form>
                    ';

                    return '<div class="d-flex">' . $exportButton . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Jika export ke Excel
        // if ($request->has('export') && $request->export === 'excel') {
        //     return Excel::download(new PayrollExport($payrolls), 'payroll-report-' . $date_from->format('Y-m') . '.xlsx');
        // }

        $departments = Department::all();

        return view('admin.payroll.index', compact('payrolls', 'date_from', 'date_to', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employee = User::whereHas('latestEmployeeJob', function ($q) {
            $q->where('employment_status', true)
                ->where('user_dakar_role', '!=', 'karyawan');
        })
            ->with([
                'latestEmployeeJob.jobWageAllowance' => function ($q) {
                    $q->where('type', 'Uang Saku');
                },
                'latestEmployeeJob.position',
                'latestEmployeeJob.department',
            ])
            ->get()
            ->sortBy(function ($user) {
                return intval(preg_replace('/\D/', '', $user->npk));
            })
            ->values() 
            ->map(function ($user) {
                $wage = $user->latestEmployeeJob->jobWageAllowance[0]?->amount ?? 0;
                $basic_salary = (int) preg_replace('/\D/', '', $wage);
                return [
                    'id' => $user->id,
                    'npk' => $user->npk,
                    'name' => $user->fullname,
                    'position' => $user->latestEmployeeJob->position?->position_name ?? '-',
                    'department' => $user->latestEmployeeJob->department?->department_name ?? '-',
                    'basic_salary' => $basic_salary,
                ];
            });

            // dd($employee);

        $mode = 'create';
        $payroll = null;
        //dd($employee);
        return view('admin.payroll.form', compact('employee', 'mode', 'payroll'));
    }

    public function edit($id)
    {
        $payroll = Payroll::with('payrollDetail')->findOrFail($id);
        
        // Get user_ids dari payroll_detail yang sudah ada
        $payrollUserIds = $payroll->payrollDetail->pluck('user_id')->unique()->toArray();
        
        // Load employees yang aktif ATAU yang ada di payroll detail
        $employee = User::where(function ($q) use ($payrollUserIds) {
                $q->whereHas('latestEmployeeJob', function ($sub) {
                    $sub->where('employment_status', true)
                        ->where('user_dakar_role', '!=', 'karyawan');
                })
                ->orWhereIn('id', $payrollUserIds);
            })
            ->with([
                'latestEmployeeJob.jobWageAllowance' => function ($q) {
                    $q->where('type', 'Uang Saku');
                },
                'latestEmployeeJob.position',
                'latestEmployeeJob.department',
            ])
            ->get()
            ->map(function ($user) {
                $wage = $user->latestEmployeeJob->jobWageAllowance[0]?->amount ?? 0;
                $basic_salary = (int) preg_replace('/\D/', '', $wage);
                return [
                    'id' => $user->id,
                    'npk' => $user->npk,
                    'name' => $user->fullname,
                    'position' => $user->latestEmployeeJob->position?->position_name ?? '-',
                    'department' => $user->latestEmployeeJob->department?->department_name ?? '-',
                    'basic_salary' => $basic_salary,
                ];
            });

        return view('admin.payroll.form', [
            'mode' => 'edit',
            'payroll' => $payroll,
            'employee' => $employee
        ]);
    }

    public function show($id)
    {
        $payroll = Payroll::with('details')->findOrFail($id);
        $employee = User::whereHas('latestEmployeeJob', function ($q) {
            $q->where('employment_status', true)
                ->where('user_dakar_role', '!=', 'karyawan');
        })
            ->with([
                'latestEmployeeJob.jobWageAllowance' => function ($q) {
                    $q->where('type', 'Uang Saku');
                },
                'latestEmployeeJob.position',
                'latestEmployeeJob.department',
            ])
            ->get()
            ->map(function ($user) {
                $wage = $user->latestEmployeeJob->jobWageAllowance[0]?->amount ?? 0;
                $basic_salary = (int) preg_replace('/\D/', '', $wage);
                return [
                    'id' => $user->id,
                    'npk' => $user->npk,
                    'name' => $user->fullname,
                    'position' => $user->latestEmployeeJob->position?->position_name ?? '-',
                    'department' => $user->latestEmployeeJob->department?->department_name ?? '-',
                    'basic_salary' => $basic_salary,
                ];
            });
        return view('admin.payroll.form', [
            'mode' => 'show',
            'payroll' => $payroll,
            'employee' => $employee
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.npk' => 'required|string',
            'details.*.user_id' => 'required',
            'details.*.user_name' => 'nullable',
            'details.*.work_days' => 'nullable|numeric',
            'details.*.attendance' => 'nullable|numeric',
            'details.*.basic_salary' => 'nullable|numeric',
            'details.*.total_salary' => 'nullable|numeric',
            'details.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $payroll = Payroll::create([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            $total_salary = 0;
            foreach ($validated['details'] as $detail) {
                $workDays = $detail['work_days'] ?? 0;
                $attendance = $detail['attendance'] ?? 0;
                $basicSalary = $detail['basic_salary'] ?? 0;

                $totalSalary = $detail['total_salary'] ?? 0;
                if ($totalSalary == 0 && $basicSalary > 0 && $workDays > 0) {
                    $totalSalary = ($basicSalary / $workDays) * $attendance;
                }

                PayrollDetail::insert([
                    'payroll_id' => $payroll->id,
                    'npk' => $detail['npk'],
                    'user_id' => $detail['user_id'],
                    'user_name' => $detail['user_name'],
                    'work_days' => $workDays,
                    'total_attend' => $attendance,
                    'basic_salary' => $basicSalary,
                    'total_salary' => $totalSalary,
                    'note' => $detail['note'] ?? null,
                ]);

                $total_salary += $totalSalary;
            }

            $payroll->total_salary = $total_salary;
            $payroll->save();

            DB::commit();

            // return redirect()->back()->with('success', 'Payroll created successfully');
            return response()->json([
                'success' => true,
                'message' => 'Payroll data saved successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // return back()->with('error', 'Error saving payroll data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error saving payroll data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.id' => 'nullable|integer', // id detail jika sudah ada
            'details.*.npk' => 'required|string',
            'details.*.user_id' => 'required',
            'details.*.user_name' => 'nullable',
            'details.*.work_days' => 'nullable|numeric',
            'details.*.attendance' => 'nullable|numeric',
            'details.*.basic_salary' => 'nullable|numeric',
            'details.*.total_salary' => 'nullable|numeric',
            'details.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Update payroll utama
            $payroll = Payroll::findOrFail($id);
            $payroll->update([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            $existingDetailIds = PayrollDetail::where('payroll_id', $id)->pluck('id')->toArray();
            $sentDetailIds = collect($validated['details'])->pluck('id')->filter()->toArray();

            // 🔹 Hapus detail yang tidak dikirim dari frontend
            $toDelete = array_diff($existingDetailIds, $sentDetailIds);
            if (!empty($toDelete)) {
                PayrollDetail::whereIn('id', $toDelete)->delete();
            }

            $total_salary = 0;

            // 🔹 Simpan atau update detail
            foreach ($validated['details'] as $detail) {
                $workDays = $detail['work_days'] ?? 0;
                $attendance = $detail['attendance'] ?? 0;
                $basicSalary = $detail['basic_salary'] ?? 0;

                $totalSalary = $detail['total_salary'] ?? 0;
                if ($totalSalary == 0 && $basicSalary > 0 && $workDays > 0) {
                    $totalSalary = ($basicSalary / $workDays) * $attendance;
                }

                if (!empty($detail['id'])) {
                    // update jika sudah ada id
                    PayrollDetail::where('id', $detail['id'])->update([
                        'npk' => $detail['npk'],
                        'user_id' => $detail['user_id'],
                        'work_days' => $workDays,
                        'total_attend' => $attendance,
                        'basic_salary' => $basicSalary,
                        'total_salary' => $totalSalary,
                        'note' => $detail['note'] ?? null,
                    ]);
                } else {
                    // insert jika baru
                    PayrollDetail::create([
                        'payroll_id' => $payroll->id,
                        'npk' => $detail['npk'],
                        'user_id' => $detail['user_id'],
                        'user_name' => $detail['user_name'],
                        'work_days' => $workDays,
                        'total_attend' => $attendance,
                        'basic_salary' => $basicSalary,
                        'total_salary' => $totalSalary,
                        'note' => $detail['note'] ?? null,
                    ]);
                }

                $total_salary += $totalSalary;
            }

            // 🔹 Update total payroll
            $payroll->total_salary = $total_salary;
            $payroll->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll updated successfully',
                'payroll_id' => $payroll->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating payroll data: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $payroll = Payroll::findOrFail($id);
            $payroll->payrollDetail()->delete();
            $payroll->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Payroll and its details have been deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while deleting the data: ' . $e->getMessage())->withInput();
        }
    }

    public function calculateWorkdays(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        $holidays = HolidayDate::whereBetween('date', [$start, $end])
            ->where('is_active', true)
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        $period = CarbonPeriod::create($start, $end);
        $workdays = 0;

        foreach ($period as $date) {
            if (!in_array($date->toDateString(), $holidays) && $date->isWeekday()) {
                $workdays++;
            }
        }

        return response()->json([
            'success' => true,
            'workdays' => $workdays,
        ]);
    }

    public function getPemagangan(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);

        // $employee = User::whereHas('employeeJob', function ($q) use ($start, $end) {
        //     $q->where('user_dakar_role', '!=', 'karyawan')
        //         ->where(function ($sub) use ($start, $end) {
        //             $sub->whereDate('start_date', '<=', $end)
        //                 ->where(function ($inner) use ($start) {
        //                     $inner->whereNull('resign_date')
        //                         ->orWhereDate('resign_date', '>=', $start);
        //                 });
        //         });
        // })

        //29 mei 2026 update ganti employeeJob() ke pemaganganOrInternshipJob()
        $employee = User::whereHas('pemaganganOrInternshipJob', function ($q) use ($start, $end) {
            $q->where('user_dakar_role', '!=', 'karyawan')
                ->whereDate('start_date', '<=', $end) 
                ->where(function ($sub) use ($start) {
                    $sub->where(function ($query) use ($start) {
                        // 2. Belum resign, atau resign-nya masih di dalam/setelah periode start
                        $query->whereNull('resign_date')
                            ->orWhereDate('resign_date', '>=', $start);
                    })
                    ->where(function ($query) use ($start) {
                        $query->whereNull('end_date') 
                            ->orWhereDate('end_date', '>=', $start);
                    });
                });
        })
            ->with([
                'pemaganganOrInternshipJob.jobWageAllowance' => function ($q) {
                    $q->where('type', 'Uang Saku');
                },
                'pemaganganOrInternshipJob.position',
                'pemaganganOrInternshipJob.department',
            ])
            ->get()
            ->map(function ($user) {
                $wage = $user->pemaganganOrInternshipJob->first()->jobWageAllowance[0]?->amount ?? 0;
                $basic_salary = (int) preg_replace('/\D/', '', $wage);
                $job = $user->pemaganganOrInternshipJob->first();

                if ($job->resign_date) {
                    $status = 'Inactive - ' . Carbon::parse($job->resign_date)->format('d M Y');
                } else {
                    $status = 'Active';
                }

                return [
                    'id'           => $user->id,
                    'npk'          => $user->npk,
                    'name'         => $user->fullname,
                    'position'     => $job->position?->position_name ?? '-',
                    'department'   => $job->department?->department_name ?? '-',
                    'basic_salary' => $basic_salary,
                    'status'       => $status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    public function exportExcel($id)
    {
        $payroll = Payroll::with('payrollDetail')->findOrFail($id);
        $fileName = "payroll-{$payroll->start_date}.xlsx";

        return Excel::download(new class($payroll) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithTitle,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize,
            \Maatwebsite\Excel\Concerns\WithCustomStartCell,
            \Maatwebsite\Excel\Concerns\WithEvents {

            private $payroll;

            public function __construct($payroll)
            {
                $this->payroll = $payroll;
            }

            public function collection()
            {
                return $this->payroll->payrollDetail->map(function ($detail, $index) {
                    return [
                        $index + 1,
                        $detail->npk,
                        $detail->user->fullname,
                        $detail->work_days,
                        $detail->total_attend,
                        $detail->basic_salary,
                        $detail->total_salary,
                        $detail->note
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    ['PERIODE: ' . $this->payroll->start_date . ' s/d ' . $this->payroll->end_date],
                    ['KETERANGAN: ' . ($this->payroll->title ?? '-')],
                    [],
                    ['No', 'NPK', 'Nama', 'Hari Kerja', 'Kehadiran', 'Gaji Pokok', 'Total Gaji', 'Keterangan']
                ];
            }

            public function startCell(): string
            {
                return 'A1'; // mulai dari baris pertama
            }

            public function registerEvents(): array
            {
                return [
                    \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();

                        // Bold untuk baris keterangan & header
                        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
                        $sheet->getStyle('A4:H4')->getFont()->setBold(true);

                        // Merge cell untuk periode dan keterangan
                        $sheet->mergeCells('A1:H1');
                        $sheet->mergeCells('A2:H2');

                        // Atur alignment ke tengah vertikal & horizontal
                        $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal('center')->setVertical('center');
                    }
                ];
            }

            public function title(): string
            {
                return 'Payroll Data';
            }
        }, $fileName);
    }
}
