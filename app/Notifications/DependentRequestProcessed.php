<?php

namespace App\Notifications;

use App\Models\DependentEditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DependentRequestProcessed extends Notification implements ShouldQueue
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
            ->subject('Permohonan Tanggungan Anda Telah Diproses')
            ->greeting('Assalamualaikum ' . $notifiable->name);

        if ($this->request->status == 'approved') {
            $message->line('Permohonan anda untuk ' . $this->getActionText() . ' telah diluluskan.');
            $message->line('Perubahan telah dikemas kini dalam akaun anda.');
        } else {
            $message->line('Permohonan anda untuk ' . $this->getActionText() . ' telah ditolak.');
            if ($this->request->admin_comments) {
                $message->line('Sebab: ' . $this->request->admin_comments);
            }
        }

        return $message
            ->action('Lihat Senarai Tanggungan Anda', url('/maklumat-ahli'))
            ->line('Terima kasih kerana menggunakan sistem e-Khairat.')
            ->salutation('Terima kasih, e-Khairat');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $statusText = $this->request->status == 'approved' ? 'diluluskan' : 'ditolak';
        
        return [
            'request_id' => $this->request->id,
            'request_type' => $this->request->request_type,
            'status' => $this->request->status,
            'comments' => $this->request->admin_comments,
            'dependent_name' => $this->request->full_name,
            'message' => 'Permohonan anda untuk ' . $this->getActionText() . ' telah ' . $statusText . '.'
        ];
    }

    /**
     * Get text description based on request type
     */
    private function getActionText(): string
    {
        switch ($this->request->request_type) {
            case 'add':
                return 'menambah tanggungan baru (' . $this->request->full_name . ')';
            case 'edit':
                return 'mengemaskini maklumat tanggungan ' . $this->request->full_name;
            case 'delete':
                return 'membuang tanggungan ' . $this->request->full_name;
            default:
                return 'memproses permintaan tanggungan';
        }
    }
}