<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Deposit_types;
use App\Models\Korwil;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Korwil::create([
            "region" => "Nagare"
        ]);

        Korwil::create([
            "region" => "Bitera"
        ]);

        Korwil::create([
            "region" => "Tabanan"
        ]);
        Korwil::create([
            "region" => "Badung"
        ]);

        Deposit_types::create([
            "deposit_name" => "Simpnanan Sukarela"
        ]);

        Deposit_types::create([
            "deposit_name" => "Tabungan Wajib"
        ]);

        Deposit_types::create([
            "deposit_name" => "Simpanan Wajib"
        ]);
        User::create([
            "email" =>  "adminbitera@gmail.com",
            "password" => "password",
            "role" => "admin"
        ]);
        User::create([
            "email" =>  "adminNegare@gmail.com",
            "password" => "password",
            "role" => "admin"
        ]);
        User::create([
            "email" =>  "pimpinankdss@gmail.com",
            "password" => "password",
            "role" => "leader"
        ]);

        // User::factory()->count(10)->create();
        Member::create([
            'name' => 'I Komang Setiana',
            'user_id' => 1,
            'korwil_id' => 2,
            'address' => 'Gianyar',
            'gender' => 'Laki-laki',
            'telp' => '0881038921',
        ]);
        Member::create([
            'name' => 'Kadek Yuli',
            'user_id' => 2,
            'korwil_id' => 1,
            'address' => 'Negare',
            'gender' => 'Laki-laki',
            'telp' => '0851934221',
        ]);
        Member::create([
            'name' => 'Putu Dharma',
            'user_id' => 3,
            'korwil_id' => 4,
            'address' => 'Badung',
            'gender' => 'Laki-laki',
            'telp' => '087428120424',
        ]);
    }
}
