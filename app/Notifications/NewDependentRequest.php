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
            ->subject('Permohonan Tanggungan Baharu Memerlukan Kelulusan')
            ->greeting('Assalamualaikum ' . $notifiable->name);

        $message->line('Permohonan baharu untuk ' . $this->getActionText() . ' memerlukan kelulusan anda.');
        $message->line('Dimohon oleh: ' . $this->request->user->name . ' (No Ahli: ' . ($this->request->user->No_Ahli ?? $this->request->user->ic_number) . ')');

        if ($this->request->comments) {
            $message->line('Catatan pemohon: ' . $this->request->comments);
        }

        return $message
            ->action('Semak Permohonan', url('/admin/dependent-edit-requests'))
            ->line('Terima kasih kerana menguruskan sistem e-Khairat!')
            ->salutation('Terima kasih, e-Khairat');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'request_type' => $this->request->request_type,
            'user_id' => $this->request->user_id,
            'user_name' => $this->request->user->name,
            'dependent_name' => $this->request->full_name,
            'message' => 'Permohonan baharu untuk ' . $this->getActionText() . ' oleh ' . $this->request->user->name . ' memerlukan kelulusan anda.'
        ];
    }

    /**
     * Get text description based on request type
     */
    private function getActionText(): string
    {
        switch ($this->request->request_type) {
            case 'add':
                return 'menambah tanggungan baharu (' . $this->request->full_name . ')';
            case 'edit':
                return 'mengemaskini maklumat tanggungan ' . $this->request->full_name;
            case 'delete':
                return 'membuang tanggungan ' . $this->request->full_name;
            default:
                return 'memproses permohonan tanggungan';
        }
    }
}
