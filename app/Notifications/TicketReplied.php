<?php

namespace App\Notifications;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The email that the owner of a ticket gets when somebody of the IT staff replies.
 * Sent from ReplyController. On your computer (MAIL_MAILER=log) the email is
 * written to storage/logs/laravel.log instead of being really sent.
 */
class TicketReplied extends Notification
{
    use Queueable;

    /**
     * The ticket that got a reply, and the reply itself.
     */
    public function __construct(
        public Ticket $ticket,
        public Reply $reply,
    ) {}

    /**
     * Get the notification's delivery channels: only email.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     * It contains the information of the ticket and of the reply.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New reply on your ticket: {$this->ticket->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->reply->user->name} from the IT and campus team replied to your ticket \"{$this->ticket->title}\":")
            ->line("\"{$this->reply->message}\"")
            ->line("The status of your ticket is now: {$this->ticket->status->label()}.")
            ->action('View your ticket', route('tickets.show', $this->ticket))
            ->line('You can answer on the ticket page.');
    }
}
