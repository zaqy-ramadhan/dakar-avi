<?php 
 
namespace App\Exports; 
 
use Carbon\Carbon; 
use Maatwebsite\Excel\Concerns\FromCollection; 
use Maatwebsite\Excel\Concerns\WithHeadings; 
use Maatwebsite\Excel\Concerns\WithColumnFormatting; 
 
class EmployeeEducationReport implements FromCollection, WithHeadings
{ 
    protected $collection; 
 
    public function __construct($collection) 
    { 
        $this->collection = $collection; 
    } 
 
    public function collection() 
    { 
        return $this->collection->map(function ($item) { 
            return [  
                'npk'               => $item['npk'], 
                'fullname'          => $item['fullname'], 
                'gender'            => $item['gender'], 
                'sd'                => $item['sd']->education_institution ?? '',
                'sd_year'           => isset($item['sd']?->education_start_year) ? ($item['sd']?->education_start_year ?? '') . ' s/d ' . ($item['sd']?->education_end_year ?? '') : '',
                'smp'               => $item['smp']->education_institution ?? '',
                'smp_year'          => isset($item['smp']?->education_start_year) ? ($item['smp']?->education_start_year ?? '') . ' s/d ' . ($item['smp']?->education_end_year ?? '') : '',
                'sma'               => $item['sma']->education_institution ?? '',
                'sma_year'          => isset($item['sma']?->education_start_year) ? ($item['sma']?->education_start_year ?? '') . ' s/d ' . ($item['sma']?->education_end_year ?? '') : '',
                'd3'                => $item['d3']->education_institution ?? '',
                'd3_year'           => isset($item['d3']?->education_start_year) ? ($item['d3']?->education_start_year ?? '') . ' s/d ' . ($item['d3']?->education_end_year ?? '') : '',
                's1'                => $item['s1']->education_institution ?? '',
                's1_year'           => isset($item['s1']?->education_start_year) ? ($item['s1']?->education_start_year ?? '') . ' s/d ' . ($item['s1']?->education_end_year ?? '') : '',
                's2'                => $item['s2']->education_institution ?? '',
                's2_year'           => isset($item['s2']?->education_start_year) ? ($item['s2']?->education_start_year ?? '') . ' s/d ' . ($item['s2']?->education_end_year ?? '') : '',
                's3'                => $item['s3']->education_institution ?? '',
                's3_year'           => isset($item['s3']?->education_start_year) ? ($item['s3']?->education_start_year ?? '') . ' s/d ' . ($item['s3']?->education_end_year ?? '') : '',


                // 'age'               => $item['age'], 
                // 'email'             => $item['email'], 
                // 'education'         => $item['education'], 
                // 'blood_type'        => $item['blood_type'], 
                
                // 'join_date'         => $item['join_date_display'] !== 'N/A' 
                //                        ? Carbon::createFromFormat('d/m/Y', $item['join_date_display']) 
                //                        : 'N/A', 
                                       
                // 'start_date'        => $item['start_date_display'] !== 'N/A' 
                //                        ? Carbon::createFromFormat('d/m/Y', $item['start_date_display']) 
                //                        : 'N/A', 
                                       
                // 'end_date'          => $item['end_date_display'] !== 'N/A' 
                //                        ? Carbon::createFromFormat('d/m/Y', $item['end_date_display']) 
                //                        : 'N/A', 
                                       
                // 'duration'          => $item['duration'], 
                // 'LOS'               => $item['LOS'], 
                // 'department'        => $item['department'], 
                // 'employment_status' => $item['employment_status'], 
                // 'job_status'        => $item['job_status'], 
                // 'job_type'          => $item['job_type'], 
                // 'gol'               => $item['gol'], 
                // 'status'            => $item['status'], 
            ]; 
        }); 
    } 
 
    public function headings(): array 
    { 
        return [ 
            'NPK', 'Fullname', 'Gender', 'SD', 'TAHUN SD', 'SMP', 'TAHUN SMP', 'SMA', 'TAHUN SMA', 'D3', 'TAHUN D3', 'S1', 'TAHUN S1', 'S2', 'TAHUN S2', 'S3', 'TAHUN S3' 
        ]; 
    } 
 
    // public function columnFormats(): array 
    // { 
    //     return [ 
    //         'H' => 'dd/mm/yyyy',  // Join Date 
    //         'I' => 'dd/mm/yyyy',  // Start Date 
    //         'J' => 'dd/mm/yyyy',  // End Date 
    //     ]; 
    // } 
}