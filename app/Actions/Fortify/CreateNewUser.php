<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserRoles;
use App\Enums\UserType;
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

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'userVariant' => [
                'required',
                'string',
                Rule::enum(UserType::class)->only(
                    [
                        UserType::BUYER,
                        UserType::SELLER,
                        UserType::LOGISTICS
                    ]
                )
            ],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $userType = UserType::from($input['userVariant']);
            $userRole = UserRoles::roleFromType($userType);
            $role = Role::where(['slug' => $userRole->value])->get();

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'type' =>  $userType->value
            ]);

            $user->roles()->attach($role);

            return $user;
        });
    }
}
