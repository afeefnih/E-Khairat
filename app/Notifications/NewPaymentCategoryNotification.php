<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PaymentCategory;

class NewPaymentCategoryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $paymentCategory;

    /**
     * Create a new notification instance.
     */
    public function __construct(PaymentCategory $paymentCategory)
    {
        $this->paymentCategory = $paymentCategory;
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
        return (new MailMessage)
                    ->subject('Kutipan Sumbangan Baharu - e-Khairat')
                    ->greeting('Assalamualaikum ' . $notifiable->name)
                    ->line('Kutipan sumbangan baharu telah ditambah ke dalam sistem e-Khairat.')
                    ->line('Maklumat Kutipan:')
                    ->line('Nama: ' . $this->paymentCategory->category_name)
                    ->line('Jumlah: RM ' . number_format($this->paymentCategory->amount, 2))
                    ->line('Deskripsi: ' . ($this->paymentCategory->category_description ?? 'Tiada deskripsi'))
                    ->action('Lihat Butiran Pembayaran', url('/dashboard'))
                    ->line('Terima kasih kerana menggunakan sistem e-Khairat.')
                    ->salutation('Terima kasih, e-Khairat');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_category_id' => $this->paymentCategory->id,
            'category_name' => $this->paymentCategory->category_name,
            'amount' => $this->paymentCategory->amount,
            'message' => 'Kutipan sumbangan baharu telah ditambah: ' . $this->paymentCategory->category_name
        ];
    }
}
