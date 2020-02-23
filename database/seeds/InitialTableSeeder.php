<?php

use Illuminate\Database\Seeder;

class InitialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            'id' => '1',
            'branch_name' => '2/F ICO - TL Aguila',
            'city' => null,
            'province' =>  null,
            'address' =>  null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('departments')->insert([
            'id' => '1',
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        DB::table('categories')->insert([
            ['id' => '1',
            'name' => 'PPE',
            'created_at' => now(),
            'updated_at' => now()],
            ['id' => '2',
            'name' => 'SEMI-EXPENDABLE',
            'created_at' => now(),
            'updated_at' => now()],
            ['id' => '3',
            'name' => 'OTHER ASSET',
            'created_at' => now(),
            'updated_at' => now()],
        ]);


        // DB::table('suppliers')->insert([
        //     'id' => '1',
        //     'name' => 'Bentacos IT',
        //     'address' => 'Ortigas, Pasig, Metro Manila',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        DB::table('roles')->insert([
            'id' => '1',
            'role_name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('roles')->insert([
            'id' => '2',
            'role_name' => 'User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
