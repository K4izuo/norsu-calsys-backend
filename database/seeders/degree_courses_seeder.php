<?php

namespace Database\Seeders;

use App\Models\DegreeCourses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class degree_courses_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $degree_courses = [
            ['office_id' => 41, 'degree_name' => 'Bachelor of Arts', 'degree_acr' => 'AB', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'Bachelor of Mass Communication', 'degree_acr' => 'BMC', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 43, 'degree_name' => 'Associate in Criminology', 'degree_acr' => 'ACRIM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 42, 'degree_name' => 'Associate in Hotel Management', 'degree_acr' => 'AHM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'Associate in Industrial Technology', 'degree_acr' => 'AIT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 50, 'degree_name' => 'Associate in Medical Dental-Nursing Assistant', 'degree_acr' => 'AMDNA', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 49, 'degree_name' => 'Assiociate in Marine Engineering', 'degree_acr' => 'AME', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 49, 'degree_name' => 'Associate in Maritime Transportation', 'degree_acr' => 'AMT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 42, 'degree_name' => 'Associate in Secretarial Science', 'degree_acr' => 'ASS', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 44, 'degree_name' => 'Bachelor of Elementary Education', 'degree_acr' => 'BEED', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 46, 'degree_name' => 'BS Agriculture', 'degree_acr' => 'BSA', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 42, 'degree_name' => 'BS Accountancy', 'degree_acr' => 'BSACCY', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'BS Aviation Maintenance', 'degree_acr' => 'BSAM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Architecture', 'degree_acr' => 'BSARCH', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 42, 'degree_name' => 'BS Business Administration', 'degree_acr' => 'BSBA', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Biology', 'degree_acr' => 'BSBIO', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Civil Engineering', 'degree_acr' => 'BSCE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Chemistry', 'degree_acr' => 'BSCHEM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Computer Engineering', 'degree_acr' => 'BSCOE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 43, 'degree_name' => 'BS Criminology', 'degree_acr' => 'BSCRIM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Computer Science', 'degree_acr' => 'BSCS', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Electronics and Communications Engineering', 'degree_acr' => 'BSECE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 44, 'degree_name' => 'Bachelor of Secondary Education', 'degree_acr' => 'BSED', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Electrical Engineering', 'degree_acr' => 'BSEE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 46, 'degree_name' => 'BS Forestry', 'degree_acr' => 'BSF', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Geodetic Engineering', 'degree_acr' => 'BSGdE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Geothermal Engineering', 'degree_acr' => 'BSGE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Geology', 'degree_acr' => 'BSGEO', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 42, 'degree_name' => 'BS Hospitality Management', 'degree_acr' => 'BSHM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Information Technology', 'degree_acr' => 'BSINT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'BS Industrial Technology', 'degree_acr' => 'BSIT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 49, 'degree_name' => 'BS Marine Engineering', 'degree_acr' => 'BSMarE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Mathematics', 'degree_acr' => 'BSMATH', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 45, 'degree_name' => 'BS Mechanical Engineering', 'degree_acr' => 'BSME', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 49, 'degree_name' => 'BS Marine Transportation', 'degree_acr' => 'BSMT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 50, 'degree_name' => 'BS Nursing', 'degree_acr' => 'BSN', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 42, 'degree_name' => 'BS Office Administration', 'degree_acr' => 'BSOA', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 50, 'degree_name' => 'BS Pharmacy', 'degree_acr' => 'BSPHARM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'BS Psychology', 'degree_acr' => 'BSPSYCH', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 84, 'degree_name' => 'BS Tourism Management', 'degree_acr' => 'BSTM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'Bachelor of Technology', 'degree_acr' => 'BT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'Bachelor of Technological Education', 'degree_acr' => 'BTE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Doctor of Management', 'degree_acr' => 'DM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'Diploma of Technology', 'degree_acr' => 'DT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 47, 'degree_name' => 'EVOC', 'degree_acr' => 'EVOC', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 41, 'degree_name' => 'Golf', 'degree_acr' => 'GOLF', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 49, 'degree_name' => 'GRCO', 'degree_acr' => 'GRCO', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 48, 'degree_name' => 'Bachelor of Laws', 'degree_acr' => 'LLB', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Doctor of Medicine', 'degree_acr' => 'MED', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 50, 'degree_name' => 'Midwifery', 'degree_acr' => 'MID', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MS Library Science', 'degree_acr' => 'MSLS', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Not Applicable', 'degree_acr' => 'NA', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Pre-Dental Medicine', 'degree_acr' => 'PDM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'High School', 'degree_acr' => 'HS', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 46, 'degree_name' => 'Associate in Agricultural Science', 'degree_acr' => 'AAS', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Expanded Tertiary Edu. Equivalency & Accre. Prog.', 'degree_acr' => 'ETEEAP', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Senior High School', 'degree_acr' => 'SHS', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 44, 'degree_name' => 'Bachelor of Science in Technical Teacher Education', 'degree_acr' => 'BTTE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 44, 'degree_name' => 'Continuing Professional Education', 'degree_acr' => 'CPE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Doctor of Dental Medicine', 'degree_acr' => 'DDM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MS Information Technology', 'degree_acr' => 'MSIT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Master of Business Administration', 'degree_acr' => 'MBA', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MS Mathematics', 'degree_acr' => 'MSMATH', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MS Agriculture', 'degree_acr' => 'MSAG', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Science Teaching', 'degree_acr' => 'MAST', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA English', 'degree_acr' => 'MAENG', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Filipino', 'degree_acr' => 'MAFIL', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA History', 'degree_acr' => 'MAHIST', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Sociology', 'degree_acr' => 'MASOCIO', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Psychology', 'degree_acr' => 'MAPSYCH', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Mathematics Teaching', 'degree_acr' => 'MAMT', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Early Childhood Education', 'degree_acr' => 'MAECE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Educational Management', 'degree_acr' => 'MAEM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Physical Education', 'degree_acr' => 'MAPE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Vocational Education', 'degree_acr' => 'MAVE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'MA Special Education', 'degree_acr' => 'MASPED', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Master of Technological Education', 'degree_acr' => 'MTE', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Master of Public Management', 'degree_acr' => 'MPM', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Master in Public Health', 'degree_acr' => 'MPH', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Doctor of Education', 'degree_acr' => 'ED.D.', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Doctor of Philosophy', 'degree_acr' => 'PH.D.', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
            ['office_id' => 58, 'degree_name' => 'Masteral', 'degree_acr' => 'MST', 'degree_inp_usr_no' => 0, 'degree_inp_timestamp' => now(), 'degree_upd_usr_no' => 0, 'degree_upd_timestamp' => now()],
        ];

        foreach($degree_courses as $course){
            DegreeCourses::firstOrCreate($course);
        }
    }
}
