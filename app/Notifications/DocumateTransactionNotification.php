<?php

namespace App\Notifications;

use App\Models\DocumateTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumateTransactionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected DocumateTransaction $transaction,
        protected string $title,
        protected string $message,
        protected ?string $url = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'transaction_id' => $this->transaction->id,
            'reference_no' => $this->transaction->reference_no,
            'status' => $this->transaction->status,
            'url' => $this->url,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->message)
            ->action('View Transaction', $this->url ?: url('/dashboard'));
    }
}
