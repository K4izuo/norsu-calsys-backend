<?php

namespace Database\Seeders;

use App\Models\Assets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class assets_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $assets = [
            // Gymnasium
            [
                'asset_name' => 'Main Gymnasium',
                'asset_type' => 'Gym',
                'capacity' => 500,
                'location' => 'Building A - Ground Floor',
                'acquisition_date' => '2020-01-15',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Indoor Sports Complex',
                'asset_type' => 'Gym',
                'capacity' => 300,
                'location' => 'Building B - 2nd Floor',
                'acquisition_date' => '2019-06-20',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Audio Visual Rooms
            [
                'asset_name' => 'AVR Room 1',
                'asset_type' => 'AVR',
                'capacity' => 100,
                'location' => 'Building C - 3rd Floor',
                'acquisition_date' => '2021-03-10',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 2,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'AVR Room 2',
                'asset_type' => 'AVR',
                'capacity' => 80,
                'location' => 'Building C - 4th Floor',
                'acquisition_date' => '2021-03-10',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 2,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Multi-Purpose AVR',
                'asset_type' => 'AVR',
                'capacity' => 150,
                'location' => 'Main Building - 1st Floor',
                'acquisition_date' => '2020-08-25',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 2,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Courts
            [
                'asset_name' => 'Basketball Court A',
                'asset_type' => 'Court',
                'capacity' => 200,
                'location' => 'Sports Complex - Outdoor',
                'acquisition_date' => '2018-05-12',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Basketball Court B',
                'asset_type' => 'Court',
                'capacity' => 200,
                'location' => 'Sports Complex - Outdoor',
                'acquisition_date' => '2018-05-12',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Volleyball Court',
                'asset_type' => 'Court',
                'capacity' => 150,
                'location' => 'Sports Complex - Indoor',
                'acquisition_date' => '2019-09-18',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Tennis Court',
                'asset_type' => 'Court',
                'capacity' => 100,
                'location' => 'Sports Complex - Outdoor',
                'acquisition_date' => '2017-11-30',
                'condition' => 'Fair',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Badminton Court',
                'asset_type' => 'Court',
                'capacity' => 80,
                'location' => 'Gymnasium - Indoor',
                'acquisition_date' => '2020-02-14',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Auditoriums
            [
                'asset_name' => 'Main Auditorium',
                'asset_type' => 'Auditorium',
                'capacity' => 800,
                'location' => 'Main Building - Ground Floor',
                'acquisition_date' => '2015-07-20',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 3,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Little Theater',
                'asset_type' => 'Auditorium',
                'capacity' => 250,
                'location' => 'Arts Building - 1st Floor',
                'acquisition_date' => '2018-10-05',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 3,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Conference Rooms
            [
                'asset_name' => 'Executive Conference Room',
                'asset_type' => 'Conference Room',
                'capacity' => 50,
                'location' => 'Administration Building - 3rd Floor',
                'acquisition_date' => '2019-04-15',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 4,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Board Room',
                'asset_type' => 'Conference Room',
                'capacity' => 30,
                'location' => 'Administration Building - 4th Floor',
                'acquisition_date' => '2019-04-15',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 4,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Laboratories
            [
                'asset_name' => 'Computer Lab 1',
                'asset_type' => 'Laboratory',
                'capacity' => 60,
                'location' => 'IT Building - 2nd Floor',
                'acquisition_date' => '2020-11-10',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 5,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Science Lab A',
                'asset_type' => 'Laboratory',
                'capacity' => 40,
                'location' => 'Science Building - 1st Floor',
                'acquisition_date' => '2019-01-22',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 5,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Multipurpose Halls
            [
                'asset_name' => 'Function Hall',
                'asset_type' => 'Multipurpose Hall',
                'capacity' => 300,
                'location' => 'Student Center - 1st Floor',
                'acquisition_date' => '2017-12-08',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 6,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Activity Center',
                'asset_type' => 'Multipurpose Hall',
                'capacity' => 400,
                'location' => 'Student Center - Ground Floor',
                'acquisition_date' => '2016-09-14',
                'condition' => 'Fair',
                'campus_id' => 1,
                'office_id' => 6,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Outdoor Facilities
            [
                'asset_name' => 'Oval Field',
                'asset_type' => 'Field',
                'capacity' => 1000,
                'location' => 'Campus Grounds - East Side',
                'acquisition_date' => '2015-03-01',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Soccer Field',
                'asset_type' => 'Field',
                'capacity' => 500,
                'location' => 'Campus Grounds - West Side',
                'acquisition_date' => '2016-06-15',
                'condition' => 'Good',
                'campus_id' => 1,
                'office_id' => 1,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Music and Dance Studios
            [
                'asset_name' => 'Music Studio',
                'asset_type' => 'Studio',
                'capacity' => 25,
                'location' => 'Arts Building - 2nd Floor',
                'acquisition_date' => '2020-05-18',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 7,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'asset_name' => 'Dance Studio',
                'asset_type' => 'Studio',
                'capacity' => 40,
                'location' => 'Arts Building - 3rd Floor',
                'acquisition_date' => '2020-05-18',
                'condition' => 'Excellent',
                'campus_id' => 1,
                'office_id' => 7,
                'availability_status' => 'Available',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // DB::table('assets')->insert($assets);
        foreach($assets as $asset){
          Assets::firstOrCreate($asset);
        }
    }
}