<?php

namespace App\Http\Controllers;

use App\DataTables\JobEmploymentDataTables;
use App\Models\Disnaker;
use App\Models\EmployeeDoc;
use App\Models\EmployeeJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\DataTables\UserDataTables;
use App\Exports\ContractExpiredExport;
use App\Exports\JoinedThisMonthExport;
use App\Exports\uniformRefreshExport;
use App\Models\DakarRole;
use App\Models\JobDoc;
use App\Models\JobWageAllowance;
use App\Exports\UsersBirthdayExport;
use App\Models\Offboarding;
use Maatwebsite\Excel\Facades\Excel;

class DocumentController extends Controller
{
    public function index($id)
    {
        $employeeJob = EmployeeJob::findOrFail($id);
        $signature = $employeeJob ? asset($employeeJob->second_party_signature) : null;
        return view('admin.users.signature', compact('id', 'signature', 'employeeJob'));
    }


    public function indexJobDocs(UserDataTables $dataTable)
    {
        if (Auth::user()->getRole() !== 'admin') {
            $user = User::with('employeeJob')->findOrFail(Auth::user()->id);
            return view('admin.job_documents.details', compact('user'));
        }

        $roles = DakarRole::whereIn('role_name', ['karyawan', 'pemagangan', 'internship'])->get();
        return $dataTable->render('admin.job_documents.index', compact('roles'));
    }

    public function jobDocsDetail(JobEmploymentDataTables $datatTable, $id)
    {
        $user = User::with('employeeJob')->findOrFail($id);
        return $datatTable->render('admin.job_documents.details', compact('user'));
    }

    public function kompensasiPDF($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'department', 'division', 'section', 'workHour')->findOrFail($id);
            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'kompensasi')->first() ?? null;
            $wages = JobWageAllowance::where('employee_job_id', $kontrak->id)->get();
            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'kompensasi';


            // Access control logic based on subGolongan
            $subGolongan = $kontrak->subGolongan->sub_golongan_name ?? '';
            $userRole = Auth::user()->getRole();

            if (in_array($subGolongan, ['4 A'])) {
                if (!in_array($userRole, ['admin', 'admin 2'])) {
                    abort(403, 'Unauthorized');
                }
            } elseif (in_array($subGolongan, [
                '4 B',
                '4 C',
                '4 D',
                '4 E',
                '4 F',
                '5 A',
                '5 B',
                '5 C',
                '5 D',
                '6 A',
                '6 B',
                '6 C',
                '6 D'
            ])) {
                if ($userRole !== 'admin') {
                    abort(403, 'Unauthorized');
                }
            } else {
                if (!in_array($userRole, ['admin', 'admin 2', 'admin 3'])) {
                    abort(403, 'Unauthorized');
                }
            }

            if ($is_admin && !$jobDoc?->first_party_signature && Auth::user()->getRole() === 'admin') {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
            }

            $pdf = PDF::loadView('documents.kompensasi', compact('kontrak', 'hr', 'jobDoc', 'wages'))
                ->setPaper('a4', 'portrait');
            $filename = 'data_kompensasi_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function kompensasiPreview($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'department', 'division', 'section', 'workHour')->findOrFail($id);
            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'kompensasi')->first() ?? null;
            $wages = JobWageAllowance::where('employee_job_id', $kontrak->id)->get();
            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'kompensasi';

            // Access control logic based on subGolongan
            $subGolongan = $kontrak->subGolongan->sub_golongan_name ?? '';
            $userRole = Auth::user()->getRole();

            if (in_array($subGolongan, ['4 A'])) {
                if (!in_array($userRole, ['admin', 'admin 2'])) {
                    abort(403, 'Unauthorized');
                }
            } elseif (in_array($subGolongan, [
                '4 B',
                '4 C',
                '4 D',
                '4 E',
                '4 F',
                '5 A',
                '5 B',
                '5 C',
                '5 D',
                '6 A',
                '6 B',
                '6 C',
                '6 D'
            ])) {
                if ($userRole !== 'admin') {
                    abort(403, 'Unauthorized');
                }
            } else {
                if (!in_array($userRole, ['admin', 'admin 2', 'admin 3'])) {
                    abort(403, 'Unauthorized');
                }
            }

            $pdf = PDF::loadView('documents.kompensasi', compact('kontrak', 'hr', 'jobDoc', 'wages'))
                ->setPaper('a4', 'portrait');
            $filename = 'data_kompensasi_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function paklaringPDF($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'department', 'division')->findOrFail($id);
            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();

            $offboarding = Offboarding::where('user_id', $kontrak->user_id)->latest() ?? null;

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'paklaring')->first() ?? null;
            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);;
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'paklaring';

            $romanMonths = [
                'I',
                'II',
                'III',
                'IV',
                'V',
                'VI',
                'VII',
                'VIII',
                'IX',
                'X',
                'XI',
                'XII'
            ];

            $month = date('n', strtotime($kontrak->start_date));
            $romanMonth = $romanMonths[$month - 1];
            $year = date('y', strtotime($kontrak->start_date));
            $kontrak->nomor = $kontrak->user->npk . '/HRD/AVI/' . $romanMonth . '/' . $year;

            if ($is_admin && !$jobDoc?->first_party_signature && Auth::user()->getRole() === 'admin') {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating.');
            }

            $pdf = PDF::loadView('documents.paklaring', compact('kontrak', 'hr', 'jobDoc', 'offboarding'))
                ->setPaper('a4', 'portrait');
            $filename = 'paklaring_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function paklaringPreview($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'department', 'division')->findOrFail($id);
            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();

            $offboarding = Offboarding::where('user_id', $kontrak->user_id)->latest();

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'paklaring')->first() ?? null;
            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);;
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'paklaring';

            $romanMonths = [
                'I',
                'II',
                'III',
                'IV',
                'V',
                'VI',
                'VII',
                'VIII',
                'IX',
                'X',
                'XI',
                'XII'
            ];

            $month = date('n', strtotime($kontrak->start_date));
            $romanMonth = $romanMonths[$month - 1];
            $year = date('y', strtotime($kontrak->start_date));
            $kontrak->nomor = $kontrak->user->npk . '/HRD/AVI/' . $romanMonth . '/' . $year;

            $pdf = PDF::loadView('documents.paklaring', compact('kontrak', 'hr', 'jobDoc', 'offboarding'))
                ->setPaper('a4', 'portrait');
            $filename = 'paklaring_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function KontrakPDF($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'subGolongan')->findOrFail($id);

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'contract')->first() ?? null;

            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);
            $employeeJob = $kontrak;
            $wages = JobWageAllowance::where('employee_job_id', $kontrak->id)->get() ?? null;
            $type = 'contract';

            // Access control logic based on subGolongan
            $subGolongan = $kontrak->subGolongan->sub_golongan_name ?? '';
            $userRole = Auth::user()->getRole();

            if ($kontrak->user_id != Auth::user()->id) {

                if (in_array($subGolongan, ['4 A'])) {
                    if (!in_array($userRole, ['admin', 'admin 2'])) {
                        abort(403, 'Unauthorized');
                    }
                } elseif (in_array($subGolongan, [
                    '4 B',
                    '4 C',
                    '4 D',
                    '4 E',
                    '4 F',
                    '5 A',
                    '5 B',
                    '5 C',
                    '5 D',
                    '6 A',
                    '6 B',
                    '6 C',
                    '6 D'
                ])) {
                    if ($userRole !== 'admin') {
                        abort(403, 'Unauthorized');
                    }
                } else {
                    if (!in_array($userRole, ['admin', 'admin 2', 'admin 3'])) {
                        abort(403, 'Unauthorized');
                    }
                }
            }

            if ($is_admin && !$jobDoc?->first_party_signature) {
                if ($kontrak->user_dakar_role === 'karyawan' && Auth::user()->getRole() === 'admin') {
                    return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
                } elseif ($kontrak->user_dakar_role !== 'karyawan' && Auth::user()->getRole() === 'admin 2') {
                    return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
                }
            }

            if (!$is_admin && !$jobDoc?->second_party_signature) {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
            }

            $romanMonths = [
                'I',
                'II',
                'III',
                'IV',
                'V',
                'VI',
                'VII',
                'VIII',
                'IX',
                'X',
                'XI',
                'XII'
            ];
            $month = date('n', strtotime($kontrak->start_date));
            $romanMonth = $romanMonths[$month - 1];
            $year = date('y', strtotime($kontrak->start_date));
            $fullyear = date('Y', strtotime($kontrak->start_date));

            if ($kontrak->user_dakar_role !== 'karyawan') {
                $disnaker = Disnaker::first();
                $hr = User::whereHas('employeeJob.position', function ($query) {
                    $query->where('position_name', 'HR & Legal Section Head');
                })->first();
                $kontrak->nomor = $kontrak->user->npk . '/HRD/AVI/' . $romanMonth . '/' . $year;
                $wage = $wages->where('type', 'Uang Saku')->first();
                $pdf = PDF::loadView('documents.kontrakPemagangan', compact('kontrak', 'hr', 'disnaker', 'jobDoc', 'wage'))
                    ->setPaper('a4', 'portrait');
                $filename = 'kontrak_pemagangan_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';
            } else {
                $hr = User::whereHas('employeeJob.position', function ($query) {
                    $query->where('position_name', 'HRGA & EHS Department Head');
                })->first();
                $first_signature = public_path('storage/' . $jobDoc?->first_party_signature);
                $kontrak->nomor = 'NPK. ' . $kontrak->user->npk . '/' . $kontrak->contract . '/AVI/' . $romanMonth . '/' . $fullyear;
                $pdf = PDF::loadView('documents.pkwt', compact('kontrak', 'hr', 'jobDoc', 'wages', 'first_signature'))
                    ->setPaper('a4', 'portrait');
                $filename = 'kontrak_pkwt_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';
            }
            PDF::setOptions(['isRemoteEnabled' => true]);
            return $pdf->stream($filename);

            // if ($is_admin) {
            //     PDF::setOptions(['isRemoteEnabled' => true]);
            //     return $pdf->stream($filename);
            // } else {
            //     // Tambahkan proteksi/enkripsi dompdf
            //     $dompdf = $pdf->getDomPDF();
            //     $dompdf->render();
            //     $dompdf->getCanvas()->get_cpdf()->setEncryption(
            //         'userpass',
            //         'ownerpass',
            //         ['print']
            //     );

            //     return $pdf->stream('kontrak.pdf');
            // }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function previewKontrak($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position')->findOrFail($id);
            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'contract')->first();
            $wages = JobWageAllowance::where('employee_job_id', $kontrak->id)->get();

            $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $month = date('n', strtotime($kontrak->start_date));
            $romanMonth = $romanMonths[$month - 1];
            $fullyear = date('Y', strtotime($kontrak->start_date));

            // Access control logic based on subGolongan
            $subGolongan = $kontrak->subGolongan->sub_golongan_name ?? '';
            $userRole = Auth::user()->getRole();

            if ($kontrak->user_id != Auth::user()->id) {
                if (in_array($subGolongan, ['4 A'])) {
                    if (!in_array($userRole, ['admin', 'admin 2'])) {
                        abort(403, 'Unauthorized');
                    }
                } elseif (in_array($subGolongan, [
                    '4 B',
                    '4 C',
                    '4 D',
                    '4 E',
                    '4 F',
                    '5 A',
                    '5 B',
                    '5 C',
                    '5 D',
                    '6 A',
                    '6 B',
                    '6 C',
                    '6 D'
                ])) {
                    if ($userRole !== 'admin') {
                        abort(403, 'Unauthorized');
                    }
                } else {
                    if (!in_array($userRole, ['admin', 'admin 2', 'admin 3'])) {
                        abort(403, 'Unauthorized');
                    }
                }
            }

            if ($kontrak->user_dakar_role !== 'karyawan') {
                $disnaker = Disnaker::first();
                $hr = User::whereHas('employeeJob.position', function ($query) {
                    $query->where('position_name', 'HR & Legal Section Head');
                })->first();
                $kontrak->nomor = $kontrak->user->npk . '/HRD/AVI/' . $romanMonth . '/' . date('y');
                $wage = $wages->where('type', 'Uang Saku')->first();
                $pdf = PDF::loadView('documents.kontrakPemagangan', compact('kontrak', 'hr', 'disnaker', 'jobDoc', 'wage'))
                    ->setPaper('a4', 'portrait');
            } else {
                $hr = User::whereHas('employeeJob.position', function ($query) {
                    $query->where('position_name', 'HRGA & EHS Department Head');
                })->first();
                $kontrak->nomor = 'NPK. ' . $kontrak->user->npk . '/' . $kontrak->contract . '/AVI/' . $romanMonth . '/' . $fullyear;
                $first_signature = public_path('storage/' . optional($jobDoc)->first_party_signature);
                $pdf = PDF::loadView('documents.pkwt', compact('kontrak', 'hr', 'jobDoc', 'wages', 'first_signature'));
            }

            return $pdf->stream('preview_kontrak.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating preview: ' . $e->getMessage());
        }
    }


    public function skhkPDF($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position')->findOrFail($id);

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'sksmk')->first() ?? null;

            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);

            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'sksmk';

            if (!$jobDoc) {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
            }

            if ($is_admin && !$jobDoc->first_party_signature) {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
            }

            if (!$is_admin && !$jobDoc->second_party_signature) {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
            }


            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();
            $pdf = PDF::loadView('documents.skhk', compact('kontrak', 'hr', 'jobDoc'))
                ->setPaper('a4', 'portrait');
            $filename = 'SKSMK_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';


            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the SKSMK: ' . $e->getMessage())->withInput();
        }
    }

    public function skhkPreview($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position')->findOrFail($id);

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'sksmk')->first() ?? null;

            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'sksmk';

            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();
            $pdf = PDF::loadView('documents.skhk', compact('kontrak', 'hr', 'jobDoc'))
                ->setPaper('a4', 'portrait');
            $filename = 'SKSMK_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';


            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the SKSMK: ' . $e->getMessage())->withInput();
        }
    }

    public function kerahasiaanPDF($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'user.firstEmployeeJob.position')->findOrFail($id);

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'kerahasiaan')->first() ?? null;

            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'kerahasiaan';

            if (!$jobDoc) {
                if (!$is_admin) {
                    return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
                }
            }

            if (!$is_admin && !$jobDoc?->second_party_signature) {
                return view('admin.users.signature', compact('id', 'employeeJob', 'type'))->with('warning', 'Please complete the signature process before generating the document.');
            }

            $pdf = PDF::loadView('documents.kerahasiaan', compact('kontrak',  'jobDoc'))
                ->setPaper('a4', 'portrait');
            $filename = 'Surat_Pernyataan_Kerahasiaan_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function kerahasiaanPreview($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDetail', 'position', 'user.firstEmployeeJob.position')->findOrFail($id);

            $jobDoc = JobDoc::where('employee_job_id', $kontrak->id)->where('type', 'kerahasiaan')->first() ?? null;

            $is_admin = in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']);
            $employeeJob = EmployeeJob::findOrFail($id);
            $type = 'kerahasiaan';

            $pdf = PDF::loadView('documents.kerahasiaan', compact('kontrak',  'jobDoc'))
                ->setPaper('a4', 'portrait');
            $filename = 'Surat_Pernyataan_Kerahasiaan_' . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }

    public function sertif($id)
    {
        try {
            $kontrak = EmployeeJob::with('user.employeeDocs')->findOrFail($id);
            $photo = optional($kontrak->user->employeeDocs)->where('doc_type', 'Pas Foto')->first() ?? '';
            // dd($photo);

            $hr = User::whereHas('employeeJob.position', function ($query) {
                $query->where('position_name', 'HRGA & EHS Department Head');
            })->first();

            $kontrak->user_dakar_role === 'internship' ? $type = 'Internship' : $type = 'Pemagangan Operator';
            if ($type == 'Pemagangan Operator') {
                $pdf = PDF::loadView('documents.sertif', compact('kontrak',  'hr', 'type', 'photo'))
                    ->setPaper('a4', 'landscape');
                $filename = 'Sertifikat_' . $type . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';
                return $pdf->stream($filename);
            } else {
                $pdf = PDF::loadView('documents.sertifIntern', compact('kontrak',  'hr', 'type', 'photo'))
                    ->setPaper('a4', 'landscape');
                $filename = 'Sertifikat_' . $type . str_replace(' ', '_', $kontrak->user->fullname) . '.pdf';
                return $pdf->stream($filename);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the document: ' . $e->getMessage())->withInput();
        }
    }


    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required|exists:dakar_employee_job,id',
            'signature' => 'required',
        ]);

        try {
            $employeeJob = EmployeeJob::findOrFail($request->id);

            $signatureData = $request->signature;

            // Cek apakah data dalam format Base64 atau langsung XML SVG
            if (strpos($signatureData, 'data:image/svg+xml;base64,') !== false) {
                // Base64 SVG, perlu didecode
                $signatureData = str_replace('data:image/svg+xml;base64,', '', $signatureData);
                $signatureData = base64_decode($signatureData);
            } elseif (strpos($signatureData, '<svg') !== false) {
                // Data langsung berupa XML SVG, tidak perlu decode
            } else {
                return back()->with('error', 'Format tanda tangan tidak valid');
            }

            // Tentukan nama file dengan ekstensi .svg
            $fileName = 'signature_' . $request->id . '_' . time() . '.svg';

            // Cari dokumen kerja terkait
            $jobDoc = JobDoc::where('employee_job_id', $employeeJob->id)
                ->where('type', $request->type)
                ->first();

            if (!$jobDoc) {
                $jobDoc = new JobDoc([
                    'employee_job_id' => $employeeJob->id,
                    'type' => $request->type,
                ]);
            }

            // Tentukan folder penyimpanan berdasarkan user role
            if (Auth::user()->id == (int) $employeeJob->user_id) {
                $filePath = "documents/second_party_signature/{$fileName}";
                $jobDoc->second_party_signature = $filePath;
            } else {
                $filePath = "documents/first_party_signature/{$fileName}";
                $jobDoc->first_party_signature = $filePath;
            }

            // Simpan file SVG
            Storage::disk('public')->put($filePath, $signatureData);

            // Simpan ke database
            $jobDoc->save();

            // Redirect dengan pesan sukses
            if (Auth::user()->id === $employeeJob->user_id) {
                return redirect()
                    ->back()
                    // ->route('users.index.job.documents')
                    ->with('success', 'Signature has been created successfully');
            }

            return redirect()->back()
                // ->route('users.index.employment.details', $employeeJob->user_id)
                ->with('success', 'Signature has been created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save signature: ' . $e->getMessage());
        }
    }

    public function exportBirthday(Request $request)
    {
        $birthMonth = $request->query('month') ?? Carbon::now()->month;
        return Excel::download(new UsersBirthdayExport($birthMonth), 'bulan-' . $birthMonth . ' birthday.xlsx');
    }

    public function expiredContract(Request $request)
    {
        $month = $request->query('month') ?? Carbon::now()->addMonths(2)->month;
        // dd($month);
        $year = $request->query('year') ?? Carbon::now()->year;
        return Excel::download(new ContractExpiredExport($month, $year), 'bulan-' . $month . '-' . $year . '-expired-contract.xlsx');
    }

    public function joinedThisMonth(Request $request)
    {
        $month = $request->query('month') ?? Carbon::now()->month;
        // dd($month);
        $year = $request->query('year') ?? Carbon::now()->year;
        return Excel::download(new JoinedThisMonthExport($month, $year), 'bulan-' . $month . '-' . $year . '-join.xlsx');
    }

    public function uniformRefresh()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        return Excel::download(new UniformRefreshExport(), 'bulan-' . $month . '-' . $year . '-uniform-refresh.xlsx');
    }
}
