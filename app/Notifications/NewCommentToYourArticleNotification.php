<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentToYourArticleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $comment)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Good morning '.$notifiable->name.'!')
            ->line('Your article <strong>'.e($this->comment->article->title).'</strong> received a new comment.')
            ->line('<em>'.e($this->comment->content).'</em>')
            ->line('This comment is awaiting validation.')
            ->action('Moderate this comment', route('userzone.comments.show', $this->comment->id))
            ->line('You can also <a href="'.route('articles.show', $this->comment->article->id).'">see it on the site</a>.')
            ->line('Thank you for stimulating discussion!')
            ->salutation("Let's stay in touch!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
