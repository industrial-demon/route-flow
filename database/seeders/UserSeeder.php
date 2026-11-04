<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'drew.naumenko@gmail.com')->exists();

        Log::info("message", [$user]);
        if (!$user) {
            /**
             * @var User
             *  */
            $user = User::create([
                'name' => 'Drew',
                'email' => 'drew.naumenko@gmail.com',
                'password' => '29Needles'
            ]);

            $user->assignRoles([UserRoles::SUPER_ADMIN->value]);
        }
    }
}
