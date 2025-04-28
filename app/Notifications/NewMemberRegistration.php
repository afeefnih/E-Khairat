<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewMemberRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;

    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pendaftaran Ahli Baru - e-Khairat')
            ->greeting('Assalamualaikum ' . $notifiable->name)
            ->line('Seorang ahli baru telah mendaftar dalam sistem e-Khairat.')
            ->line('Maklumat Ahli:')
            ->line('Nama: ' . $this->newUser->name)
            ->line('No. Kad Pengenalan: ' . $this->newUser->ic_number)
            ->line('E-mel: ' . ($this->newUser->email ?? 'Tiada e-mel'))
            ->line('Alamat: ' . $this->newUser->address)
            ->action('Lihat Butiran Ahli', url('/admin/users/' . $this->newUser->id . '/edit'))
            ->line('Sila semak pendaftaran ini jika perlu.')
            ->salutation('Terima kasih, e-Khairat');
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->newUser->id,
            'name' => $this->newUser->name,
            'ic_number' => $this->newUser->ic_number,
            'message' => 'Ahli baru telah mendaftar: ' . $this->newUser->name . ' (' . $this->newUser->ic_number . ')'
        ];
    }
}
