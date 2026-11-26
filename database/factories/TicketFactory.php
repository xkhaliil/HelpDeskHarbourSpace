<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Realistic problems, grouped by the name of their category.
     *
     * @var array<string, list<array{0: string, 1: string}>>
     */
    protected const PROBLEMS = [
        'Wi-Fi & Network' => [
            ['Wi-Fi keeps disconnecting in the classroom', 'The student Wi-Fi drops every few minutes during the lecture. I have to reconnect each time and I lose my video call. It happens on my laptop and on my phone.'],
            ['No internet on the 3rd floor', 'Since this morning nobody on the third floor can open a website. The Wi-Fi shows as connected, but pages never load.'],
            ['Very slow Wi-Fi in the library', 'Downloads in the library are extremely slow. It is impossible to download the course material or to join an online meeting.'],
            ['Ethernet port not working at my desk', 'The network port at the desk next to the window gives no connection. I tried two different cables and two laptops.'],
            ['Guests cannot use the guest Wi-Fi', 'The visitors for our workshop cannot log in to the guest Wi-Fi. The login page does not load after they accept the terms.'],
            ['Wi-Fi password rejected on my new phone', 'My new phone says that my password is wrong for the student network, but the same password works on my laptop.'],
        ],
        'Accounts & Portal' => [
            ['Cannot log in to the student portal', 'I get "invalid credentials" when I log in to the student portal, even after resetting my password twice.'],
            ['Password reset email never arrives', 'I requested a password reset three times, but the email never arrives. It is not in my spam folder either.'],
            ['A course is missing in my portal', 'I am enrolled in the Data Structures module, but it does not appear in my portal, so I cannot see the assignments.'],
            ['Two-factor code is not accepted', 'The six-digit code from my authenticator app is always rejected. I checked that the time on my phone is correct.'],
            ['Grades page shows an error', 'When I open the grades page in the portal I only see "Something went wrong". It still worked last week.'],
            ['Account locked after too many attempts', 'My account was locked after I typed a wrong password a few times. I need it unlocked before my exam tomorrow.'],
        ],
        'Classroom Equipment' => [
            ['Projector does not turn on', 'The projector does not react to the remote or to the power button. The light on the device stays red.'],
            ['Broken HDMI cable at the teacher desk', 'The HDMI cable at the teacher desk is damaged. The image keeps flickering and then disappears completely.'],
            ['Microphone makes a loud buzzing noise', 'The wireless microphone makes a loud buzz as soon as it is switched on, so the students at the back cannot hear the lecture.'],
            ['No markers or eraser in the room', 'There are no working whiteboard markers and no eraser in the room. Could someone bring new ones before the next class?'],
            ['Smart screen does not respond to touch', 'The touch function of the smart screen no longer works. Only the mouse still works.'],
            ['No sound from the room speakers', 'The room speakers give no sound when we join a video call, although the laptop shows that they are connected.'],
        ],
        'Laptop & Software' => [
            ['MATLAB licence has expired', 'For the Numerical Methods course I need MATLAB, but my licence expired yesterday. Can it be renewed?'],
            ['Loan laptop does not start', 'The loan laptop I borrowed this morning does not start, even when it is plugged in.'],
            ['Cannot install Python packages in the lab', 'On the lab computers "pip install" fails with a permission error, so I cannot run the exercises of the course.'],
            ['Office keeps asking for activation', 'Word and Excel keep asking me to activate Office with my university account, but the activation always fails.'],
            ['Lab laptop overheats and shuts down', 'One of the laptops in the lab gets very hot and shuts down after about twenty minutes.'],
            ['VPN client crashes on start', 'The VPN client closes right after I start it, so I cannot reach the internal servers from home.'],
        ],
        'Building & Facilities' => [
            ['Air conditioning is not working', 'It is very hot in the room and the air conditioning does not respond to the controls on the wall.'],
            ['Broken chair in the classroom', 'One of the chairs in the second row has a broken leg. It is dangerous to sit on.'],
            ['Access card not accepted at the entrance', 'My access card is not accepted by the reader at the main entrance after 8 pm, so I cannot get back in.'],
            ['Water leak next to the coffee machine', 'There is water on the floor next to the coffee machine. It looks like the machine is leaking.'],
            ['Lights flickering in the corridor', 'The lights in the corridor have been flickering all day. It is very tiring for the eyes.'],
            ['Toilet out of order on the ground floor', 'One of the toilets on the ground floor does not flush anymore.'],
        ],
    ];

    /**
     * Places on campus where a problem can happen.
     *
     * @var list<string>
     */
    protected const LOCATIONS = [
        'Room 1.02',
        'Room 1.14',
        'Room 2.03',
        'Room 2.11',
        'Room 3.05',
        'Lecture Hall A',
        'Lecture Hall B',
        'Library, 1st floor',
        'Computer lab, 2nd floor',
        'Coworking space, ground floor',
        'Meeting room 4',
        'Reception',
        'Cafeteria',
        '3rd floor corridor',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryName = fake()->randomElement(array_keys(self::PROBLEMS));
        [$title, $description] = fake()->randomElement(self::PROBLEMS[$categoryName]);
        $createdAt = fake()->dateTimeBetween('-2 months');

        return [
            'user_id' => User::factory(),
            // Use the category that matches the problem, so the title and the category fit together.
            'category_id' => fn () => Category::firstOrCreate(['name' => $categoryName])->id,
            'title' => $title,
            'description' => $description,
            'location' => fake()->randomElement(self::LOCATIONS),
            'priority' => fake()->randomElement([
                TicketPriority::Normal, TicketPriority::Normal, TicketPriority::Normal,
                TicketPriority::High, TicketPriority::High,
                TicketPriority::Urgent,
            ]),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'screenshot' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => ['status' => TicketStatus::Open]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => ['status' => TicketStatus::InProgress]);
    }

    public function solved(): static
    {
        return $this->state(fn (array $attributes) => ['status' => TicketStatus::Solved]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => TicketStatus::Closed]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => ['priority' => TicketPriority::Urgent]);
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => ['priority' => TicketPriority::High]);
    }
}
