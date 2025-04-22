<?php

namespace App\Notifications;

use App\Models\DependentEditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDependentRequest extends Notification implements ShouldQueue
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
            ->subject('New Dependent Request Requires Approval')
            ->greeting('Hello ' . $notifiable->name . '!');

        $message->line('A new request to ' . $this->getActionText() . ' requires your approval.');
        $message->line('Requested by: ' . $this->request->user->name);

        return $message
            ->action('Review Request', url('/admin/dependent-edit-requests'))
            ->line('Thank you for managing our system!');
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
            'user_id' => $this->request->user_id,
            'user_name' => $this->request->user->name,
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
