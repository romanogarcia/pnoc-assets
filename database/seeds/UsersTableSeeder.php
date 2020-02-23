<?php

use Illuminate\Database\Seeder;
use App\Helpers\Utility;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => '1',
            'username' => 'admin',
            'email' => 'test@test.com',
            'password' =>  Hash::make('secret'),
            'slug_token' =>  Utility::generate_unique_token(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('employees')->insert([
            'id' => '1',
            'employee_no' => '2019081601',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'address' =>  'Metro Manila, Philippines',
            'is_active' => '1',
            'user_id' => '1', //Connected to the user 1
            'department_id' => '1', //Connected to the department 1
            // 'location_id' => '1', //Connected to the location 1
            'slug_token' => Utility::generate_unique_token(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_roles')->insert([
            'id' => '1',
            'user_id' => '1',
            'role_id' =>  '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
