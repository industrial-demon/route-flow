<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserRoles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input += ['userVariant' => UserRoles::BAYER->value];

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'userVariant' => ['required', 'string', Rule::in([
                UserRoles::BAYER->value,
                UserRoles::SELLER->value,
            ])],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $role = Role::firstOrCreate(['slug' => $input['userVariant']]);

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            $user->roles()->attach($role);

            return $user;
        });
    }
}
