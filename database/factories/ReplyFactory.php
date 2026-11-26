<?php

namespace Database\Factories;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Messages written by the student or teacher who reported the problem.
     *
     * @var list<string>
     */
    protected const USER_MESSAGES = [
        'Thanks! It happened again this morning.',
        'It is still not working, unfortunately.',
        'It works now, thank you very much for the quick help!',
        'Is there any update? I have a class there tomorrow at 9:00.',
        'It only happens in the afternoon. In the morning everything is fine.',
        'A classmate has exactly the same problem.',
        'I will be there until 18:00 if someone can come by.',
        'Thanks for the quick answer!',
    ];

    /**
     * Messages written by the IT and campus team (admins).
     *
     * @var list<string>
     */
    protected const STAFF_MESSAGES = [
        'Thanks for reporting this. A colleague will come by this afternoon.',
        'We are looking into it and will keep you updated here.',
        'We ordered a replacement part. It should arrive in two or three days.',
        'Could you tell us at what time the problem happens?',
        'We made a change on our side. Can you check if it works now?',
        'This is a known issue and our supplier is working on it. We will keep you updated.',
        'We fixed the problem. Please let us know if it happens again.',
        'We could not reproduce the problem. Could you add more details to the ticket?',
        'A colleague is on the way to check it now.',
        'Thanks for your patience, we are almost done.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'message' => fake()->randomElement(self::USER_MESSAGES),
        ];
    }

    /**
     * A reply written by an admin of the IT and campus team.
     */
    public function fromStaff(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->admin(),
            'message' => fake()->randomElement(self::STAFF_MESSAGES),
        ]);
    }
}
