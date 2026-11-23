<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->unique()->lastName();

        return [
            'name' => $firstName.' '.$lastName,
            // A student address that matches the name, like "marta.vidal@student.harbour.space".
            'email' => Str::slug($firstName.' '.$lastName, '.').'@'.config('helpdesk.student_email_domain'),
            'role' => Role::Student,
            'approved_at' => now(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * An account that is still waiting for an admin to approve it.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => null,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Student,
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Teacher,
            'email' => Str::slug($attributes['name'], '.').'@'.config('helpdesk.teacher_email_domain'),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Admin,
            'email' => Str::slug($attributes['name'], '.').'@harbour.space',
        ]);
    }
}
