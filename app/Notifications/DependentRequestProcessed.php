<?php

namespace App\Notifications;

use App\Models\DependentEditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DependentRequestProcessed extends Notification
{
    use Queueable;

    protected $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(DependentEditRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Your Dependent Request Has Been Processed')
            ->greeting('Hello ' . $notifiable->name . '!');

        if ($this->request->status == 'approved') {
            $message->line('Your request to ' . $this->getActionText() . ' has been approved.');
            $message->line('The changes have been applied to your account.');
        } else {
            $message->line('Your request to ' . $this->getActionText() . ' has been rejected.');
            if ($this->request->admin_comments) {
                $message->line('Reason: ' . $this->request->admin_comments);
            }
        }

        return $message
            ->action('View Your Dependents', url('/maklumat-ahli'))
            ->line('Thank you for using our system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'request_type' => $this->request->request_type,
            'status' => $this->request->status,
            'comments' => $this->request->admin_comments,
            'dependent_name' => $this->request->full_name,
        ];
    }

    /**
     * Get text description based on request type
     */
    private function getActionText(): string
    {
        switch ($this->request->request_type) {
            case 'add':
                return 'add a new dependent (' . $this->request->full_name . ')';
            case 'edit':
                return 'edit dependent information for ' . $this->request->full_name;
            case 'delete':
                return 'delete the dependent ' . $this->request->full_name;
            default:
                return 'process a dependent request';
        }
    }
}
