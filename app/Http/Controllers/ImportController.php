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
                    'religion' => $row[21],
                    'no_jamsostek' => $row[22],
                    'no_npwp' => $row[23],
                    'no_ktp' => $row[24],
                    'no_phone_house' => $row[25],
                    'no_phone' => $row[26],
                    'ktp_address' => $row[28],
                    'current_address' => $row[29],
                    'emergency_contact' => $row[30],
                    'tax_status' => $row[31],
                    'marital_status' => $row[32],
                    'married_year' => $row[34],
                ]);

                if($row[33]){ //pasangan
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'pasangan',
                        'name' => $row[33],
                    ], [
                        'birth_date' => $row[35],
                        'education' => $row[36],
                        'occupation' => $row[37],
                    ]);
                }

                if($row[38]){ //anak
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[38],
                    ], [
                        'birth_date' => $row[39],
                        'education' => $row[40],
                        'occupation' => $row[41],
                    ]);
                }

                if($row[42]){ //anak 2
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[42],
                    ], [
                        'birth_date' => $row[43],
                        'education' => $row[44],
                        'occupation' => $row[45],
                    ]);
                }

                if($row[46]){ //anak 3
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'child',
                        'name' => $row[46],
                    ], [
                        'birth_date' => $row[47],
                        'education' => $row[48],
                        'occupation' => $row[49],
                    ]);
                }

                if($row[50]){ //ayah
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'ayah',
                        'name' => $row[50],
                    ], [
                        'birth_date' => $row[51],
                        'education' => $row[52],
                        'occupation' => $row[53],
                    ]);
                }

                if($row[54]){ //ibu
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'ibu',
                        'name' => $row[54],
                    ], [
                        'birth_date' => $row[55],
                        'education' => $row[56],
                        'occupation' => $row[57],
                    ]);
                }

                if($row[58]){ //saudara
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'saudara',
                        'name' => $row[58],
                    ], [
                        'birth_date' => $row[59],
                        'education' => $row[60],
                        'occupation' => $row[61],
                    ]);
                }

                if($row[62]){ //saudara 2
                    $employeeFamily = EmployeeFamily::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => 'saudara',
                        'name' => $row[62],
                    ], [
                        'birth_date' => $row[63],
                        'education' => $row[64],
                        'occupation' => $row[65],
                    ]);
                }

                if($row[66]){ //education 1
                    $employeeEducation = EmployeeEducation::updateOrCreate([
                        'user_id' => $user->id,
                        'education_level' => $row[66],
                    ], [
                        'education_institution' => $row[67],
                        'education_city' => $row[68],
                        'education_major' => $row[69],
                        'education_gpa' => $row[70],
                        'education_start_year' => $row[71],
                        'education_end_year' => $row[72],
                    ]);
                }

                if($row[73]){ //education 2
                    $employeeEducation = EmployeeEducation::updateOrCreate([
                        'user_id' => $user->id,
                        'education_level' => $row[73],
                    ], [
                        'education_institution' => $row[74],
                        'education_city' => $row[75],
                        'education_major' => $row[76],
                        'education_gpa' => $row[77],
                        'education_start_year' => $row[78],
                        'education_end_year' => $row[79],
                    ]);
                }

                if($row[80]){//training 1
                    $employeeTraining = EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[81],
                        'training_year' => $row[82],
                    ], [
                        'training_duration' => $row[80],
                        'training_certificate' => $row[83],
                    ]);
                }

                if($row[84]){//training 2
                    $employeeTraining = EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[85],
                        'training_year' => $row[86],
                    ], [
                        'training_duration' => $row[84],
                        'training_certificate' => $row[87],
                    ]);
                }

                if($row[88]){//training 3
                    $employeeTraining = EmployeeTraining::updateOrCreate([
                        'user_id' => $user->id,
                        'training_institution' => $row[89],
                        'training_year' => $row[90],
                    ], [
                        'training_duration' => $row[88],
                        'training_certificate' => $row[91],
                    ]);
                }

                if($row[92]){//bank
                    $employeeBank = EmployeeBank::updateOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'bank_name' => $row[92],
                        'account_name' => $row[93],
                        'account_number' => $row[94],
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
                $pos = Position::whereRaw('LOWER(position_name) = ?', [strtolower($row[11])])->first();
                $lvl = Level::whereRaw('LOWER(level_name) = ?', [strtolower($row[12])])->first();
                $jtype = JobType::whereRaw('LOWER(job_type_name) = ?', [strtolower($row[13])])->first();
                $line = Line::whereRaw('LOWER(line_name) = ?', [strtolower($row[14])])->first();
                $gol = Golongan::whereRaw('LOWER(golongan_name) = ?', [strtolower($row[15])])->first();
                $subgol = SubGolongan::whereRaw('LOWER(sub_golongan_name) = ?', [strtolower($row[16])])->first();
                $role = DakarRole::whereRaw('LOWER(role_name) = ?', [strtolower($row[20])])->first();

                if($row[95] == 'Aktif'){
                    $jobStatus = true;
                } elseif($row[95] == 'Nonaktif'){
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
                    'position_id' => $pos ? $pos->id : null,
                    'role_level_id' => $lvl ? $lvl->id : null,
                    'job_type_id' => $jtype ? $jtype->id : null,
                    'line_id' => $line ? $line->id : null,
                    'golongan_id' => $gol ? $gol->id : null,
                    'sub_golongan_id' => $subgol ? $subgol->id : null,
                    'job_status' => strtolower($row[17]),
                    'start_date' => $row[18],
                    'end_date' => $row[19],
                    'user_dakar_role' => strtolower($row[20]),
                    'is_onboarding_completed' => true,
                    'employment_status' => $jobStatus,
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
            Log::error('Import failed: ' . $e->getMessage());

            // Return error message to user
            return back()->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }
}
