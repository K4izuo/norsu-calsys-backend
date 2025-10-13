<?php

namespace Database\Seeders;

use App\Models\Offices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class offices_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            ['office_code' => '002', 'office_name' => 'Office of the Vice-President for Administration and Finance', 'office_acr' => 'OVAF', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '008', 'office_name' => 'Chief Administrative Office-Administration', 'office_acr' => 'OCAA', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '009', 'office_name' => 'Chief Administrative Office-Finance', 'office_acr' => 'OCAF', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '010', 'office_name' => 'Accounting Office', 'office_acr' => 'OUAC', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '011', 'office_name' => 'University Human Resource Development Management', 'office_acr' => 'HRDM', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '013', 'office_name' => 'BAC Secretariat (Public Bidding)', 'office_acr' => 'BSPB', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '076', 'office_name' => 'BAC Secretariat (Alternative Mode Procurement)', 'office_acr' => 'BSAMP', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '006', 'office_name' => 'Office of the University Board Secretary', 'office_acr' => 'OUBS', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '014', 'office_name' => 'Budget Office', 'office_acr' => 'OUBO', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '015', 'office_name' => 'University General Services Unit', 'office_acr' => 'OUGS', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '027', 'office_name' => 'Commission on Audit Office', 'office_acr' => 'OCOA', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '043', 'office_name' => 'Internal Audit Office', 'office_acr' => 'OUIA', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '072', 'office_name' => 'Other GASS', 'office_acr' => 'OGAS', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '059', 'office_name' => 'Office of the University Security and Safety Management', 'office_acr' => 'USSM', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '067', 'office_name' => 'Supply Office', 'office_acr' => 'OUSO', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '012', 'office_name' => 'Alumni Affairs Office', 'office_acr' => 'ODAA', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '029', 'office_name' => 'Dental Office', 'office_acr' => 'OUMD', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '035', 'office_name' => 'Gender and Development', 'office_acr' => 'ODGD', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '037', 'office_name' => 'Guidance / CARE Office', 'office_acr' => 'OGCO', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '038', 'office_name' => 'IGP Office', 'office_acr' => 'OIGP', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '039', 'office_name' => 'IMD Office', 'office_acr' => 'OIMD', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '040', 'office_name' => 'Office of the University Management Information System', 'office_acr' => 'UMIS', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '041', 'office_name' => 'Public Information Office', 'office_acr' => 'PIO', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '046', 'office_name' => 'Library Office', 'office_acr' => 'OTUL', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '047', 'office_name' => 'Medical Office', 'office_acr' => 'OUMS', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '048', 'office_name' => 'Mini-Hotel', 'office_acr' => 'OUMH', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '049', 'office_name' => 'Motor Pool / Vehicle Operations and Maintenance', 'office_acr' => 'OUFP', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '051', 'office_name' => 'NSF Office', 'office_acr' => 'ONSF', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '052', 'office_name' => 'NSTP / ROTC Office', 'office_acr' => 'OUNR', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '075', 'office_name' => 'Other STO Services', 'office_acr' => 'OSTO', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '054', 'office_name' => 'Planning Office', 'office_acr' => 'ODPD', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '055', 'office_name' => 'QUAMC Office', 'office_acr' => 'ODQA', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '058', 'office_name' => 'Scholarship Office', 'office_acr' => 'OUSO', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '061', 'office_name' => 'Sports and Athletics Office', 'office_acr' => 'OUSA', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '063', 'office_name' => 'Student Affairs Services Office', 'office_acr' => 'ODSA', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '064', 'office_name' => 'Student Government 1 Office', 'office_acr' => 'OSG1', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '065', 'office_name' => 'Student Government 2 Office', 'office_acr' => 'OSG2', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '066', 'office_name' => 'Student Publication / The Norsunian Office', 'office_acr' => 'OUSP', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '069', 'office_name' => 'Yearbook', 'office_acr' => 'OUYB', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '003', 'office_name' => 'Office of the Vice-President for Academic Affairs Office', 'office_acr' => 'OVAA', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '017', 'office_name' => 'College of Arts and Sciences', 'office_acr' => 'OCAS', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '018', 'office_name' => 'College of Business Administration', 'office_acr' => 'OCBA', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '019', 'office_name' => 'College of Criminal Justice Education', 'office_acr' => 'OCCJ', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '020', 'office_name' => 'College of Teacher Education', 'office_acr' => 'CTED', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '021', 'office_name' => 'College of Engineering and Architecture', 'office_acr' => 'OCEA', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '022', 'office_name' => 'College of Agriculture, Forestry and Fishery ', 'office_acr' => 'OCFF', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '023', 'office_name' => 'College of Industrial Technology', 'office_acr' => 'OCIT', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '024', 'office_name' => 'College of Law', 'office_acr' => 'OCLW', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '025', 'office_name' => 'College of Maritime Education', 'office_acr' => 'OCME', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '026', 'office_name' => 'College of Nursing, Pharmacy and Allied Health Sciences', 'office_acr' => 'OCAH', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '028', 'office_name' => 'Cultural Office', 'office_acr' => 'OUCA', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '031', 'office_name' => 'English Department', 'office_acr' => 'ODEN', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '032', 'office_name' => 'ETEEAP', 'office_acr' => 'ETAP', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '050', 'office_name' => 'NBC 461 Local Eval Committee', 'office_acr' => 'ONBC', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '073', 'office_name' => 'Other Higher Ed Services', 'office_acr' => 'OHES', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '053', 'office_name' => 'P.E. Department', 'office_acr' => 'ODPE', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '062', 'office_name' => 'Student Teaching Support Office', 'office_acr' => 'OSTS', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '036', 'office_name' => 'Graduate School Office', 'office_acr' => 'OUGS', 'office_pap_code' => '3 02 01 0000', 'office_pap_no' => 5, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '070', 'office_name' => 'Other Advance Education Services', 'office_acr' => 'OAES', 'office_pap_code' => '3 02 01 0000', 'office_pap_no' => 5, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '005', 'office_name' => 'OLD REXIL (NOT IN USE)', 'office_acr' => 'N/A', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 0, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '042', 'office_name' => 'Innovation and Technology Support Office', 'office_acr' => 'OITO', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '044', 'office_name' => 'University International Relations', 'office_acr' => 'OUIR', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '045', 'office_name' => 'ITSO Office', 'office_acr' => 'OITS', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '057', 'office_name' => 'Research Department', 'office_acr' => 'OURD', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '074', 'office_name' => 'Other Research Services', 'office_acr' => 'OORS', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '004', 'office_name' => 'Office of the Vice-President for Research, Development, and Extension', 'office_acr' => 'ORDE', 'office_pap_code' => '3 04 01 0000', 'office_pap_no' => 7, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '033', 'office_name' => 'EVOC', 'office_acr' => 'EVOC', 'office_pap_code' => '3 04 01 0000', 'office_pap_no' => 7, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '034', 'office_name' => 'Extension Department', 'office_acr' => 'ODCE', 'office_pap_code' => '3 04 01 0000', 'office_pap_no' => 7, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '071', 'office_name' => 'Other Extension Services', 'office_acr' => 'OEXS', 'office_pap_code' => '3 04 01 0000', 'office_pap_no' => 7, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '016', 'office_name' => 'Cashier\'s Office', 'office_acr' => 'OUCD', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '001', 'office_name' => 'University President\'s Office', 'office_acr' => 'OTUP', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
            ['office_code' => '007', 'office_name' => 'Campus Director', 'office_acr' => 'OCAD', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '068', 'office_name' => 'University Farm Manager\'s Office', 'office_acr' => 'OUFM', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '030', 'office_name' => 'University Project Management', 'office_acr' => 'OUPM', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '056', 'office_name' => 'Registrar\'s Office', 'office_acr' => 'OURO', 'office_pap_code' => '2 00 01 0000', 'office_pap_no' => 3, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '0071', 'office_name' => 'Assistant Campus Administrator\'s Office - Administration', 'office_acr' => 'ACAA', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '0072', 'office_name' => 'Assistant Campus Administrator\'s Office - Academics', 'office_acr' => 'ACAD', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '077', 'office_name' => 'Safety Compliance Office', 'office_acr' => 'OSCO', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '078', 'office_name' => 'Clonal', 'office_acr' => 'OCNL', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '079', 'office_name' => 'Payroll Department', 'office_acr' => 'PYRL', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '080', 'office_name' => 'Curriculum and Instruction', 'office_acr' => 'ODCI', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '081', 'office_name' => 'Affiliated Renewable Energy Center', 'office_acr' => 'AREC', 'office_pap_code' => '3 03 01 0000', 'office_pap_no' => 6, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '0081', 'office_name' => 'Environmental Management Office', 'office_acr' => 'ENVMO', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '082', 'office_name' => 'College of Tourism and Hospitality Management', 'office_acr' => 'OTHM', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 1, 'office_is_one' => 0],
            ['office_code' => '083', 'office_name' => 'Records Management Office', 'office_acr' => 'RMO', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '086', 'office_name' => 'Procurement Unit', 'office_acr' => 'PROC', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '9999', 'office_name' => 'MIS - Electronic Data Processing / Digital Media', 'office_acr' => 'EDP-DM', 'office_pap_code' => '1 00 01 0000', 'office_pap_no' => 1, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '99999', 'office_name' => 'Others', 'office_acr' => 'OTHERS', 'office_pap_code' => '0 00 00 0000', 'office_pap_no' => 9999, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 0],
            ['office_code' => '084', 'office_name' => 'Office of the Vice President for Student Affairs and Services', 'office_acr' => 'OVSS', 'office_pap_code' => '3 01 01 0000', 'office_pap_no' => 4, 'office_show' => 1, 'office_is_college' => 0, 'office_is_one' => 1],
        ];

        foreach($offices as $office){
            Offices::firstOrCreate($office);
        }
    }
}
