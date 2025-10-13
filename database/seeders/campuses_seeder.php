<?php

namespace Database\Seeders;

use App\Models\Campuses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class campuses_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campuses = [
            ['campus_name' => 'Main Campus', 'campus_acr' => 'MAIN'],
            ['campus_name' => 'Bais Campus', 'campus_acr' => 'BAIS'],
            ['campus_name' => 'Bayawan-Sta. Catalina Campus', 'campus_acr' => 'BSC'],
            ['campus_name' => 'Guihulngan Campus', 'campus_acr' => 'GUIH'],
            ['campus_name' => 'Mabinay Campus', 'campus_acr' => 'MAB'],
            ['campus_name' => 'Pamplona Campus', 'campus_acr' => 'PAMP'],
            ['campus_name' => 'Siaton Campus', 'campus_acr' => 'SIA'],
            ['campus_name' => 'Others', 'campus_acr' => 'OTHERS'],
        ];

        foreach($campuses as $campus){
            Campuses::firstOrCreate($campus);
        }
    }
}
