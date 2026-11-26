<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Realistic categories of a university HelpDesk, with the description of each one.
     *
     * @var array<string, string>
     */
    private const DESCRIPTIONS = [
        'Wi-Fi & Network' => 'The Wi-Fi that does not connect or keeps dropping, network cables and the VPN.',
        'Accounts & Portal' => 'Logging in to your university account, passwords and the student portal.',
        'Classroom Equipment' => 'Projectors, screens, microphones and speakers in the classrooms.',
        'Laptop & Software' => 'University laptops and the software you need for your courses.',
        'Building & Facilities' => 'Heating, air conditioning, lights, doors and anything else in the building.',
        'Printing & Scanning' => 'Printers and scanners that do not work, are out of paper or out of toner.',
        'Audio & Video' => 'Video calls, recording lectures and the audio systems in the event rooms.',
        'Email & Calendar' => 'Your university email address, shared calendars and mailing lists.',
        'Access Cards & Security' => 'Access cards that do not open a door, lost cards and the security cameras.',
        'Furniture & Rooms' => 'Broken chairs and tables, and rooms that are not set up for a class.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(array_keys(self::DESCRIPTIONS)),
            // The description is looked up with the name, so they always match,
            // also when a test or the seeder chooses the name itself.
            'description' => fn (array $attributes) => self::DESCRIPTIONS[$attributes['name']] ?? 'Other problems on campus.',
        ];
    }
}
