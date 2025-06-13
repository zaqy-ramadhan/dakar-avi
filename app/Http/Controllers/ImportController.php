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
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;

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
                    'npk' => $row[0]
                ], [
                    'fullname' => $row[1],
                    'username' => $row[0],
                    'email' => $row[2],
                    'join_date' => $row[3],
                    'password' => 'Avi123!',
                    'password_hash' => bcrypt('Avi123!'),
                ]);
                
                if ($row[5] == 'L') {
                    $gender = 0;
                } elseif ($row[5] == 'P') {
                    $gender = 1;
                }

                $employeeDetail = EmployeeDetail::updateOrCreate([
                    'user_id' => $user->id,
                ], [
                    'birth_date' => $row[4],
                    'gender' => $gender,
                    'birth_place' => $row[6],
                    'blood_type' => $row[7],
                    'religion' => $row[23],
                    'no_jamsostek' => $row[24],
                    'no_npwp' => $row[25],
                    'no_ktp' => $row[26],
                    'no_phone_house' => $row[27],
                    'no_phone' => $row[28],
                    'ktp_address' => $row[30],
                    'current_address' => $row[31],
                    'emergency_contact' => $row[32],
                    'tax_status' => $row[33],
                    'marital_status' => $row[34],
                    'married_year' => $row[36],
                ]);

                if($row[35]){ //pasangan
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'pasangan',
                        'name' => $row[35],
                    ], [
                        'birth_date' => $row[37],
                        'education' => $row[38],
                        'occupation' => $row[39],
                    ]);
                }

                if($row[40]){ //anak
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[40],
                    ], [
                        'birth_date' => $row[41],
                        'education' => $row[42],
                        'occupation' => $row[43],
                    ]);
                }

                if($row[44]){ //anak 2
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[44],
                    ], [
                        'birth_date' => $row[45],
                        'education' => $row[46],
                        'occupation' => $row[47],
                    ]);
                }

                if($row[48]){ //anak 3
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[48],
                    ], [
                        'birth_date' => $row[49],
                        'education' => $row[50],
                        'occupation' => $row[51],
                    ]);
                }

                if($row[52]){ //ayah
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'ayah',
                        'name' => $row[52],
                    ], [
                        'birth_date' => $row[53],
                        'education' => $row[54],
                        'occupation' => $row[55],
                    ]);
                }

                if($row[56]){ //ibu
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'ibu',
                        'name' => $row[56],
                    ], [
                        'birth_date' => $row[57],
                        'education' => $row[58],
                        'occupation' => $row[59],
                    ]);
                }

                if($row[60]){ //saudara
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'saudara',
                        'name' => $row[60],
                    ], [
                        'birth_date' => $row[61],
                        'education' => $row[62],
                        'occupation' => $row[63],
                    ]);
                }

                if($row[64]){ //saudara 2
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'saudara',
                        'name' => $row[64],
                    ], [
                        'birth_date' => $row[65],
                        'education' => $row[66],
                        'occupation' => $row[67],
                    ]);
                }

                if($row[68]){ //education 1
                    $employeeEducation = EmployeeEducation::updateOrCreate([
                        'user_id' => $user->id,
                        'education_level' => $row[68],
                    ], [
                        'education_institution' => $row[69],
                        'education_city' => $row[70],
                        'education_major' => $row[71],
                        'education_gpa' => $row[72],
                        'education_start_year' => $row[73],
                        'education_end_year' => $row[74],
                    ]);
                }

                if($row[75]){ //education 2
                    $employeeEducation = EmployeeEducation::updateOrCreate([
                        'user_id' => $user->id,
                        'education_level' => $row[75],
                    ], [
                        'education_institution' => $row[76],
                        'education_city' => $row[77],
                        'education_major' => $row[78],
                        'education_gpa' => $row[79],
                        'education_start_year' => $row[80],
                        'education_end_year' => $row[81],
                    ]);
                }

                if($row[82]){//training 1
                    $employeeTraining = EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[83],
                        'training_year' => $row[84],
                    ], [
                        'training_duration' => $row[82],
                        'training_certificate' => $row[85],
                    ]);
                }

                if($row[86]){//training 2
                    $employeeTraining = EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[87],
                        'training_year' => $row[88],
                    ], [
                        'training_duration' => $row[86],
                        'training_certificate' => $row[89],
                    ]);
                }

                if($row[90]){//training 3
                    $employeeTraining = EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[91],
                        'training_year' => $row[92],
                    ], [
                        'training_duration' => $row[90],
                        'training_certificate' => $row[93],
                    ]);
                }

                if($row[94]){//bank
                    $employeeBank = EmployeeBank::updateOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'bank_name' => $row[94],
                        'account_name' => $row[95],
                        'account_number' => $row[96],
                    ]);
                }

                // if($row[95]){//document Ijazah dan Transkrip
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'Ijazah dan Transkrip',
                //     ], [
                //         'doc_path' => $row[95],
                //     ]);
                // }

                // if($row[96]){//document KTP
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'KTP',
                //     ], [
                //         'doc_path' => $row[96],
                //     ]);
                // }

                // if($row[97]){//document NPWP
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'NPWP',
                //     ], [
                //         'doc_path' => $row[97],
                //     ]);
                // }

                // if($row[98]){//document SIM
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'SIM',
                //     ], [
                //         'doc_path' => $row[98],
                //     ]);
                // }

                // if($row[99]){//document Kartu keluarga
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'Kartu Keluarga',
                //     ], [
                //         'doc_path' => $row[99],
                //     ]);
                // }

                // if($row[100]){//document Akte Kelahiran
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'Akte Kelahiran',
                //     ], [
                //         'doc_path' => $row[100],
                //     ]);
                // }

                // if($row[101]){//document Akte Kelahiran Anak
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'Akte Kelahiran Anak',
                //     ], [
                //         'doc_path' => $row[101],
                //     ]);
                // }

                // if($row[102]){//document Buku Nikah
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'Buku Nikah',
                //     ], [
                //         'doc_path' => $row[102],
                //     ]);
                // }

                // if($row[95]){//document Pas Foto
                //     $employeeDoc = EmployeeDoc::updateOrCreate([
                //         'user_id' => $user->id,
                //         'doc_type' => 'Pas Foto',
                //     ], [
                //         'doc_path' => $row[103],
                //     ]);
                // }

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
                $subgol = SubGolongan::whereRaw('LOWER(sub_golongan_name) = ?', [strtolower($row[18])])->first();
                $role = DakarRole::whereRaw('LOWER(role_name) = ?', [strtolower($row[22])])->first();

                if($row[97] == 'Aktif'){
                    $jobStatus = true;
                } elseif($row[97] == 'Nonaktif'){
                    $jobStatus = false;
                } else {
                    $jobStatus = null;
                }

                $employeeJob = EmployeeJob::updateOrCreate([
                    'user_id' => $user->id
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
                    'start_date' => $row[20],
                    'end_date' => $row[21],
                    'user_dakar_role' => strtolower($row[22]),
                    'is_onboarding_completed' => true,
                    'employment_status' => $jobStatus,
                    'work_hour_code_id' => $work ? $work->id : null

                ]);

                $user->dakarRole()->sync($role->id);

                // $item = Item::firstOrCreate([
                //     'name' => $row[2]
                // ], [
                //     'size' => $row[3]
                // ]);

                // Inventory::updateOrCreate([
                //     'user_id' => $user->id,
                //     'item_id' => $item->id,
                // ]);
            }

            return back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            // Log error message
            Log::error('Import failed: ' . $e);

            // Return error message to user
            return back()->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }
}
