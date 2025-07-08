<?php

namespace App\Http\Controllers;

use App\Models\DakarRole;
use App\Models\Department;
use App\Models\Division;
use App\Models\EmployeeBank;
use App\Models\EmployeeDetail;
use App\Models\EmployeeDoc;
use App\Models\EmployeeEducation;
use App\Models\EmployeeFamily;
use App\Models\EmployeeJob;
use App\Models\EmployeeTraining;
use App\Models\Golongan;
use App\Models\Group;
use App\Models\SubGolongan;
use App\Models\User;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\JobType;
use App\Models\Level;
use App\Models\Line;
use App\Models\Position;
use App\Models\Section;
use App\Models\WorkHour;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use function PHPSTORM_META\type;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.users.import');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls'
            ]);

            $data = Excel::toArray([], $request->file('file'));

            foreach ($data[0] as $index => $row) {
                if ($index == 0) continue;

                $user = User::updateOrCreate([
                    'npk' => (string)$row[0]
                ], [
                    'fullname' => $row[1],
                    'username' => $row[0],
                    'email' => $row[2],
                    'join_date' => $this->parseExcelDate($row[3]),
                    'password' => 'Avi123!',
                    'password_hash' => bcrypt('Avi123!'),
                ]);

                $gender = null;
                if ($row[5] == 'L') {
                    $gender = 0;
                } elseif ($row[5] == 'P') {
                    $gender = 1;
                }

                $employeeDetail = EmployeeDetail::updateOrCreate([
                    'user_id' => $user->id,
                ], [
                    'birth_date' => $this->parseExcelDate($row[4]),
                    'gender' => $gender,
                    'birth_place' => $row[6],
                    'blood_type' => $row[7],
                    'religion' => $row[23 + 8],
                    'no_jamsostek' => $row[24 + 8],
                    'no_npwp' => $row[25 + 8],
                    'no_ktp' => $row[26 + 8],
                    'no_phone_house' => $row[27 + 8],
                    'no_phone' => $row[28 + 8],
                    'ktp_address' => $row[30 + 8],
                    'current_address' => $row[31 + 8],
                    'emergency_contact' => $row[32 + 8],
                    'tax_status' => $row[33 + 8],
                    'marital_status' => $row[34 + 8],
                    'married_year' => $row[36 + 8],
                    'is_draft' => false,
                ]);

                if ($row[35 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'pasangan',
                        'name' => $row[35 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[37 + 8]),
                        'education' => $row[38 + 8],
                        'occupation' => $row[39 + 8],
                    ]);
                }

                if ($row[40 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[40 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[41 + 8]),
                        'education' => $row[42 + 8],
                        'occupation' => $row[43 + 8],
                    ]);
                }

                if ($row[44 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[44 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[45 + 8]),
                        'education' => $row[46 + 8],
                        'occupation' => $row[47 + 8],
                    ]);
                }

                if ($row[48 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[48 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[49 + 8]),
                        'education' => $row[50 + 8],
                        'occupation' => $row[51 + 8],
                    ]);
                }

                if ($row[52 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'ayah',
                        'name' => $row[52 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[53 + 8]),
                        'education' => $row[54 + 8],
                        'occupation' => $row[55 + 8],
                    ]);
                }

                if ($row[56 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'ibu',
                        'name' => $row[56 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[57 + 8]),
                        'education' => $row[58 + 8],
                        'occupation' => $row[59 + 8],
                    ]);
                }

                if ($row[60 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'saudara',
                        'name' => $row[60 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[61 + 8]),
                        'education' => $row[62 + 8],
                        'occupation' => $row[63 + 8],
                    ]);
                }

                if ($row[64 + 8]) {
                    EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'saudara',
                        'name' => $row[64 + 8],
                    ], [
                        'birth_date' => $this->parseExcelDate($row[65 + 8]),
                        'education' => $row[66 + 8],
                        'occupation' => $row[67 + 8],
                    ]);
                }

                if ($row[68 + 8]) {
                    EmployeeEducation::updateOrCreate([
                        'user_id' => $user->id,
                        'education_level' => $row[68 + 8],
                    ], [
                        'education_institution' => $row[69 + 8],
                        'education_city' => $row[70 + 8],
                        'education_major' => $row[71 + 8],
                        'education_gpa' => $row[72 + 8],
                        'education_start_year' => $row[73 + 8],
                        'education_end_year' => $row[74 + 8],
                    ]);
                }

                if ($row[75 + 8]) {
                    EmployeeEducation::updateOrCreate([
                        'user_id' => $user->id,
                        'education_level' => $row[75 + 8],
                    ], [
                        'education_institution' => $row[76 + 8],
                        'education_city' => $row[77 + 8],
                        'education_major' => $row[78 + 8],
                        'education_gpa' => $row[79 + 8],
                        'education_start_year' => $row[80 + 8],
                        'education_end_year' => $row[81 + 8],
                    ]);
                }

                if ($row[82 + 8]) {
                    EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[83 + 8],
                        'training_year' => $row[84 + 8],
                    ], [
                        'training_duration' => $row[82 + 8],
                        'training_certificate' => $row[85 + 8],
                    ]);
                }

                if ($row[86 + 8]) {
                    EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[87 + 8],
                        'training_year' => $row[88 + 8],
                    ], [
                        'training_duration' => $row[86 + 8],
                        'training_certificate' => $row[89 + 8],
                    ]);
                }

                if ($row[90 + 8]) {
                    EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[91 + 8],
                        'training_year' => $row[92 + 8],
                    ], [
                        'training_duration' => $row[90 + 8],
                        'training_certificate' => $row[93 + 8],
                    ]);
                }

                if ($row[94 + 8]) {
                    EmployeeBank::updateOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'bank_name' => $row[94 + 8],
                        'account_name' => $row[95 + 8],
                        'account_number' => $row[96 + 8],
                    ]);
                }

                $group = Group::whereRaw('LOWER(group_name) = ?', [strtolower($row[8])])->first();
                $div = Division::whereRaw('LOWER(division_name) = ?', [strtolower($row[9])])->first();
                $dept = Department::whereRaw('LOWER(department_name) = ?', [strtolower($row[10])])->first();
                $sec = Section::whereRaw('LOWER(section_name) = ?', [strtolower($row[11])])->first();
                $pos = Position::whereRaw('LOWER(position_name) = ?', [strtolower($row[12])])->first();
                $lvl = Level::whereRaw('LOWER(level_name) = ?', [strtolower($row[13])])->first();
                $jtype = JobType::whereRaw('LOWER(job_type_name) = ?', [strtolower($row[14])])->first();
                $work = WorkHour::whereRaw('LOWER(work_hour) = ?', [strtolower($row[15])])->first();
                $line = Line::whereRaw('LOWER(line_name) = ?', [strtolower($row[16])])->first();
                $gol = Golongan::whereRaw('LOWER(golongan_name) = ?', [strtolower($row[17])])->first();
                $subgolInput = strtolower(preg_replace('/(\d)([a-zA-Z])/', '$1 $2', $row[18]));
                $subgol = SubGolongan::whereRaw('LOWER(sub_golongan_name) = ?', [$subgolInput])->first();
                $role = DakarRole::whereRaw('LOWER(role_name) = ?', [strtolower($row[30])])->first();

                if ($row[97+8] == 'Aktif') {
                    $jobStatus = true;
                } elseif ($row[97+8] == 'Inactive') {
                    $jobStatus = false;
                } else {
                    $jobStatus = null;
                }
                // dd(strtolower($row[19]));
                $lastJob = null;
                for ($i = 0; $i < 5; $i++) {
                    // $startDate = $row[20 + ($i * 2)];
                    // $endDate = $row[21 + ($i * 2)];

                    $startDateRaw = $row[20 + ($i * 2)];
                    $endDateRaw   = $row[21 + ($i * 2)];

                    $startDate = $this->parseExcelDate($startDateRaw);
                    $endDate   = $this->parseExcelDate($endDateRaw);

                    $user_role = DakarRole::findOrFail($role->id)->role_name;

                    if ($startDate && $endDate) {

                        $employmentStatus = true;
                        for ($j = $i + 1; $j < 5; $j++) {
                            $nextStart = $this->parseExcelDate($row[20 + ($j * 2)]);
                            $nextEnd   = $this->parseExcelDate($row[21 + ($j * 2)]);
                            if ($nextStart && $nextEnd) {
                                $employmentStatus = false;
                                break;
                            }
                        }

                        if($lastJob === null){
                            $lastJob = EmployeeJob::where('user_id', $user->id)
                                ->where('start_date', '<', $startDate)
                                ->latest('start_date')
                                ->first();
                        }

                        $requestDummy = new Request([
                            'job_status' => strtolower($row[19]),
                            'position_id' => $pos ? $pos->id : null,
                            'level_id' => $lvl ? $lvl->id : null,
                            'department_id' => $dept ? $dept->id : null,
                            'division_id' => $div ? $div->id : null,
                        ]);

                        $notes = $this->determineJobNotes(
                            $lastJob,
                            strtolower($user_role),
                            $requestDummy,
                            $lastJob === null
                        );

                        // if ($startDate && $endDate) {
                            $create = EmployeeJob::updateOrCreate([
                                'user_id' => $user->id,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                            ], [
                                'group_id' => $group ? $group->id : null,
                                'division_id' => $div ? $div->id : null,
                                'department_id' => $dept ? $dept->id : null,
                                'section_id' => $sec ? $sec->id : null,
                                'position_id' => $pos ? $pos->id : null,
                                'role_level_id' => $lvl ? $lvl->id : null,
                                'job_type_id' => $jtype ? $jtype->id : null,
                                'line_id' => $line ? $line->id : null,
                                'golongan_id' => $gol ? $gol->id : null,
                                'sub_golongan_id' => $subgol ? $subgol->id : null,
                                'job_status' => strtolower($row[19]),
                                'user_dakar_role' => strtolower($row[30]),
                                'is_onboarding_completed' => true,
                                'employment_status' => ($jobStatus !== true) ? $jobStatus : $employmentStatus,
                                'work_hour_code_id' => $work ? $work->id : null,
                                'notes' => $notes
                            ]);

                            $lastJob = $create;
                        // }
                    }

                    $user->dakarRole()->sync($role->id);
                }
            }


            return back()->with('success', 'Import berhasil!');
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }


    /**
     * Helper parse date
     */
    private function parseExcelDate($value)
    {
        if (!$value) return null;
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        if (strlen($value) === 6) { // ex: 210625
            return Carbon::createFromFormat('dmy', $value)->format('Y-m-d');
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    private function determineJobNotes(EmployeeJob|null $lastJob, string $user_role, Request $request, bool $isFirstJob): string
    {
        // dd($isFirstJob);
        if ($isFirstJob || $lastJob === null) {
            if ($user_role === 'karyawan') {
                return match ($request->job_status) {
                    'tetap' => 'New Employee Tetap',
                    'asing' => 'New Employee Asing',
                    default => 'New Employee Kontrak',
                };
            } elseif ($user_role === 'pemagangan') {
                return 'New Employee Pemagangan';
            } elseif ($user_role === 'internship') {
                return 'New Employee Internship';
            }
        } else {
            if ($user_role === 'karyawan') {
                if (
                    $lastJob->position_id !== $request->position_id ||
                    $lastJob->role_level_id !== $request->level_id ||
                    $lastJob->department_id !== $request->department_id ||
                    $lastJob->division_id !== $request->division_id
                ) {
                    return 'Employee Transfer';
                } else {
                    return 'Extension Contract';
                }
            } elseif ($user_role === 'pemagangan') {
                return 'Employee Pemagangan Extension';
            } elseif ($user_role === 'internship') {
                return 'Employee Internship Extension';
            }
        }

        return '';
    }

    // public function import_old(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'file' => 'required|mimes:xlsx,xls'
    //         ]);

    //         $data = Excel::toArray([], $request->file('file'));

    //         foreach ($data[0] as $index => $row) {
    //             if ($index == 0) continue;

    //             $user = User::updateOrCreate([
    //                 'npk' => $row[0]
    //             ], [
    //                 'fullname' => $row[1],
    //                 'username' => $row[0],
    //                 'email' => $row[2],
    //                 'join_date' => $row[3],
    //                 'password' => 'Avi123!',
    //                 'password_hash' => bcrypt('Avi123!'),
    //             ]);

    //             if ($row[5] == 'L') {
    //                 $gender = 0;
    //             } elseif ($row[5] == 'P') {
    //                 $gender = 1;
    //             }

    //             $employeeDetail = EmployeeDetail::updateOrCreate([
    //                 'user_id' => $user->id,
    //             ], [
    //                 'birth_date' => $this->parseExcelDate($row[4],
    //                 'gender' => $gender,
    //                 'birth_place' => $row[6],
    //                 'blood_type' => $row[7],
    //                 'religion' => $row[23],
    //                 'no_jamsostek' => $row[24],
    //                 'no_npwp' => $row[25],
    //                 'no_ktp' => $row[26],
    //                 'no_phone_house' => $row[27],
    //                 'no_phone' => $row[28],
    //                 'ktp_address' => $row[30],
    //                 'current_address' => $row[31],
    //                 'emergency_contact' => $row[32],
    //                 'tax_status' => $row[33],
    //                 'marital_status' => $row[34],
    //                 'married_year' => $row[36],
    //             ]);

    //             if ($row[35]) { //pasangan
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'pasangan',
    //                     'name' => $row[35],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[37],
    //                     'education' => $row[38],
    //                     'occupation' => $row[39],
    //                 ]);
    //             }

    //             if ($row[40]) { //anak
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'child',
    //                     'name' => $row[40],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[41],
    //                     'education' => $row[42],
    //                     'occupation' => $row[43],
    //                 ]);
    //             }

    //             if ($row[44]) { //anak 2
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'child',
    //                     'name' => $row[44],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[45],
    //                     'education' => $row[46],
    //                     'occupation' => $row[47],
    //                 ]);
    //             }

    //             if ($row[48]) { //anak 3
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'child',
    //                     'name' => $row[48],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[49],
    //                     'education' => $row[50],
    //                     'occupation' => $row[51],
    //                 ]);
    //             }

    //             if ($row[52]) { //ayah
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'ayah',
    //                     'name' => $row[52],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[53],
    //                     'education' => $row[54],
    //                     'occupation' => $row[55],
    //                 ]);
    //             }

    //             if ($row[56]) { //ibu
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'ibu',
    //                     'name' => $row[56],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[57],
    //                     'education' => $row[58],
    //                     'occupation' => $row[59],
    //                 ]);
    //             }

    //             if ($row[60]) { //saudara
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'saudara',
    //                     'name' => $row[60],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[61],
    //                     'education' => $row[62],
    //                     'occupation' => $row[63],
    //                 ]);
    //             }

    //             if ($row[64]) { //saudara 2
    //                 $employeeFamily = EmployeeFamily::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'type' => 'saudara',
    //                     'name' => $row[64],
    //                 ], [
    //                     'birth_date' => $this->parseExcelDate($row[65],
    //                     'education' => $row[66],
    //                     'occupation' => $row[67],
    //                 ]);
    //             }

    //             if ($row[68]) { //education 1
    //                 $employeeEducation = EmployeeEducation::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'education_level' => $row[68],
    //                 ], [
    //                     'education_institution' => $row[69],
    //                     'education_city' => $row[70],
    //                     'education_major' => $row[71],
    //                     'education_gpa' => $row[72],
    //                     'education_start_year' => $row[73],
    //                     'education_end_year' => $row[74],
    //                 ]);
    //             }

    //             if ($row[75]) { //education 2
    //                 $employeeEducation = EmployeeEducation::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'education_level' => $row[75],
    //                 ], [
    //                     'education_institution' => $row[76],
    //                     'education_city' => $row[77],
    //                     'education_major' => $row[78],
    //                     'education_gpa' => $row[79],
    //                     'education_start_year' => $row[80],
    //                     'education_end_year' => $row[81],
    //                 ]);
    //             }

    //             if ($row[82]) { //training 1
    //                 $employeeTraining = EmployeeTraining::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'training_institution' => $row[83],
    //                     'training_year' => $row[84],
    //                 ], [
    //                     'training_duration' => $row[82],
    //                     'training_certificate' => $row[85],
    //                 ]);
    //             }

    //             if ($row[86]) { //training 2
    //                 $employeeTraining = EmployeeTraining::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'training_institution' => $row[87],
    //                     'training_year' => $row[88],
    //                 ], [
    //                     'training_duration' => $row[86],
    //                     'training_certificate' => $row[89],
    //                 ]);
    //             }

    //             if ($row[90]) { //training 3
    //                 $employeeTraining = EmployeeTraining::updateOrCreate([
    //                     'user_id' => $user->id,
    //                     'training_institution' => $row[91],
    //                     'training_year' => $row[92],
    //                 ], [
    //                     'training_duration' => $row[90],
    //                     'training_certificate' => $row[93],
    //                 ]);
    //             }

    //             if ($row[94]) { //bank
    //                 $employeeBank = EmployeeBank::updateOrCreate([
    //                     'user_id' => $user->id,
    //                 ], [
    //                     'bank_name' => $row[94],
    //                     'account_name' => $row[95],
    //                     'account_number' => $row[96],
    //                 ]);
    //             }

    //             $group = Group::whereRaw('LOWER(group_name) = ?', [strtolower($row[8])])->first();
    //             $div = Division::whereRaw('LOWER(division_name) = ?', [strtolower($row[9])])->first();
    //             $dept = Department::whereRaw('LOWER(department_name) = ?', [strtolower($row[10])])->first();
    //             $sec = Section::whereRaw('LOWER(section_name) = ?', [strtolower($row[11])])->first();
    //             $pos = Position::whereRaw('LOWER(position_name) = ?', [strtolower($row[12])])->first();
    //             $lvl = Level::whereRaw('LOWER(level_name) = ?', [strtolower($row[13])])->first();
    //             $jtype = JobType::whereRaw('LOWER(job_type_name) = ?', [strtolower($row[14])])->first();
    //             $work = WorkHour::whereRaw('LOWER(work_hour) = ?', [strtolower($row[15])])->first();
    //             $line = Line::whereRaw('LOWER(line_name) = ?', [strtolower($row[16])])->first();
    //             $gol = Golongan::whereRaw('LOWER(golongan_name) = ?', [strtolower($row[17])])->first();
    //             $subgolInput = strtolower(preg_replace('/(\d)([a-zA-Z])/', '$1 $2', $row[18]));
    //             $subgol = SubGolongan::whereRaw('LOWER(sub_golongan_name) = ?', [$subgolInput])->first();
    //             $role = DakarRole::whereRaw('LOWER(role_name) = ?', [strtolower($row[22])])->first();

    //             if ($row[97] == 'Aktif') {
    //                 $jobStatus = true;
    //             } elseif ($row[97] == 'Nonaktif') {
    //                 $jobStatus = false;
    //             } else {
    //                 $jobStatus = null;
    //             }

    //             $employeeJob = EmployeeJob::updateOrCreate([
    //                 'user_id' => $user->id
    //             ], [
    //                 'group_id' => $group ? $group->id : null,
    //                 'division_id' => $div ? $div->id : null,
    //                 'department_id' => $dept ? $dept->id : null,
    //                 'section_id' => $sec ? $sec->id : null,
    //                 'position_id' => $pos ? $pos->id : null,
    //                 'role_level_id' => $lvl ? $lvl->id : null,
    //                 'job_type_id' => $jtype ? $jtype->id : null,
    //                 'line_id' => $line ? $line->id : null,
    //                 'golongan_id' => $gol ? $gol->id : null,
    //                 'sub_golongan_id' => $subgol ? $subgol->id : null,
    //                 'job_status' => strtolower($row[19]),
    //                 'start_date' => $row[20],
    //                 'end_date' => $row[21],
    //                 'user_dakar_role' => strtolower($row[22]),
    //                 'is_onboarding_completed' => true,
    //                 'employment_status' => $jobStatus,
    //                 'work_hour_code_id' => $work ? $work->id : null

    //             ]);

    //             $user->dakarRole()->sync($role->id);

    //             // $item = Item::firstOrCreate([
    //             //     'name' => $row[2]
    //             // ], [
    //             //     'size' => $row[3]
    //             // ]);

    //             // Inventory::updateOrCreate([
    //             //     'user_id' => $user->id,
    //             //     'item_id' => $item->id,
    //             // ]);
    //         }

    //         return back()->with('success', 'Data berhasil diimport!');
    //     } catch (\Exception $e) {
    //         // Log error message
    //         Log::error('Import failed: ' . $e);

    //         // Return error message to user
    //         return back()->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
    //     }
    // }
}
