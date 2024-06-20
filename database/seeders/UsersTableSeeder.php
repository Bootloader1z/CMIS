<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed multiple sample users
        $users = [
            [
                'id' => 1,
                'fullname' => 'Super Admin',
                'username' => 'admin',
                'email' => 'administrator@tas.com',
                'password' => Crypt::encryptString('P@s$w0rd123'),
                'email_verified_at' => '2024-04-25 13:15:39',
                'role' => 9,
                'isactive' => 0,
                'created_at' => '2024-04-25 13:15:39',
                'updated_at' => '2024-05-31 16:27:02',
            ],
            [
                'id' => 2,
                'fullname' => 'OJT SAINT',
                'username' => 'saint',
                'email' => 'saint@tas.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => '2024-04-25 13:15:39',
                'role' => 2,
                'isactive' => 0,
                'created_at' => '2024-04-25 13:15:39',
                'updated_at' => '2024-05-31 16:27:02',
            ],
            [
                'id' => 3,
                'fullname' => 'Administrator TAS',
                'username' => 'mark',
                'email' => 'mark@tas.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => '2024-04-25 13:15:39',
                'role' => 2,
                'isactive' => 1,
                'created_at' => '2024-04-25 13:15:39',
                'updated_at' => '2024-05-08 07:27:01',
            ],
            [
                'id' => 5,
                'fullname' => 'Micah Truinfo',
                'username' => 'micah',
                'email' => 'micah@gmail.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => null,
                'role' => 2,
                'isactive' => 1,
                'created_at' => '2024-04-26 00:37:19',
                'updated_at' => '2024-05-07 07:53:46',
            ],
            [
                'id' => 6,
                'fullname' => 'Germalyn Saysay',
                'username' => 'gem123',
                'email' => 'germalyn@gmail.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => null,
                'role' => 2,
                'isactive' => 1,
                'created_at' => '2024-04-26 00:39:56',
                'updated_at' => '2024-05-07 01:35:41',
            ],
            [
                'id' => 7,
                'fullname' => 'Joven Cordeta',
                'username' => 'joven',
                'email' => 'joven@gmail.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => null,
                'role' => 2,
                'isactive' => 0,
                'created_at' => '2024-04-29 00:27:54',
                'updated_at' => '2024-06-03 09:36:00',
            ],
            [
                'id' => 9,
                'fullname' => 'Mac Mac The Pogi',
                'username' => 'markcalleja',
                'email' => 'markcalleja@gmail.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => null,
                'role' => 2,
                'isactive' => 1,
                'created_at' => '2024-04-29 00:43:30',
                'updated_at' => '2024-04-29 00:54:56',
            ],
            [
                'id' => 10,
                'fullname' => 'OJT JICO',
                'username' => 'jico',
                'email' => 'jekjek@tas.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => null,
                'role' => 2,
                'isactive' => 1,
                'created_at' => '2024-05-05 17:03:52',
                'updated_at' => '2024-05-17 08:43:20',
            ],
            [
                'id' => 11,
                'fullname' => 'Jdc E',
                'username' => 'Jdc E',
                'email' => 'JDC@gmail.com',
                'password' => Crypt::encryptString('password123'),
                'email_verified_at' => null,
                'role' => 0,
                'isactive' => 1,
                'created_at' => '2024-05-10 02:37:54',
                'updated_at' => '2024-05-10 02:38:06',
            ],
        ];

        // Insert each user into the users table
        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
