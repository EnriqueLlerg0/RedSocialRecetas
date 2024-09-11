<?php

namespace Database\Seeders;

use App\Helpers\GeneralHelper;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Provider\es_ES\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('es_ES');

        $users = [];
        $labels = [
            'administrador',
            'jefe administrador',
            'jefe comercial',
            'jefe comercial',
            'comercial',
            'comercial',
            'súper usuario',
            'súper usuario',
            'súper usuario',
            'usuario'
        ];

        $user_id = '';
        for ($i = 10; $i >= 1; $i--) {
            $city = $faker->city;

            $permissions = GeneralHelper::getDefaultPermissions();

            if ($i == 1) array_push($permissions, 'ACPACON');

            $user = User::insertGetId([
                'firstName' => $i == 1 ? 'Jose J' : $faker->firstName('male'),
                'lastName' => $i == 1 ? 'Ariza' : $faker->lastName('male'),
                'gender' => 'M',
                'accountVerifiedAt' => '',
                'profileImage' => 'default.jpg',
                'isActive' => true,
                'label' => $labels[$i-1],
                'email' => "email$i@email.com",
                'phone' => $i == 1 ? '605117004' : ($i == 4 ? '605117001' : '60511700' . $i),
                'password' => Hash::make("1234"),
                'province' => $city,
                'town' => $city,
                'address' => 'Dirección de casa',
                'postal' => '14010',
                'dni' => Person::dni(),
                'responsible' => [$user_id],
                'verification_code' => '',
                'devices' => [],
                'favDevices' => [],
                'favRoutes' => [],
                'createdAt' => Carbon::now()->format('Y-m-d H:i:s'),
                'permissions' => $permissions
            ], '_id');

            $users[] = $user;
            $user_id = $user;
        }

        foreach (array_reverse($users) as $i => $u) {
            $city = $faker->city;

            $user = User::insertGetId([
                'firstName' => $faker->firstName('male'),
                'lastName' => $faker->lastName('male'),
                'gender' => 'M',
                'label' => $labels[9],
                'accountVerifiedAt' => '',
                'isActive' => true,
                'profileImage' => 'default.jpg',
                'email' => "subemail$i@email.com",
                'phone' => '60511700' . $i,
                'password' => Hash::make('1234'),
                'province' => $city,
                'town' => $city,
                'address' => 'Dirección de casa',
                'postal' => '14010',
                'dni' => Person::dni(),
                'responsible' => [$u],
                'verification_code' => '',
                'devices' => [],
                'favDevices' => [],
                'favRoutes' => [],
                'createdAt' => Carbon::now()->format('Y-m-d H:i:s'),
                'permissions' => ['GESDIS', 'GESU', 'ACPACON']
            ], '_id');
        }
    }
}
