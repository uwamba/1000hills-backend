<?php
namespace App\Mail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class PaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string  $paymentUrl;

    public function __construct(Booking $booking, string $paymentUrl)
    {
        $this->booking    = $booking;
        $this->paymentUrl = $paymentUrl;
    }

    public function build()
    {
        return $this
            ->subject("Complete your payment for Booking #{$this->booking->id}")
            ->view('emails.payment_form_link')
            ->with([
                'booking'    => $this->booking,
                'paymentUrl' => $this->paymentUrl,
            ]);
    }
}
