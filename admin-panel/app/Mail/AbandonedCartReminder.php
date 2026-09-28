<?php

namespace App\Mail;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbandonedCartReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $abandonedCart;
    public $reminderType; // 'first', 'second', 'third'
    public $discountCoupon;

    /**
     * Create a new message instance.
     */
    public function __construct(AbandonedCart $abandonedCart, $reminderType = 'first', $discountCoupon = null)
    {
        $this->abandonedCart = $abandonedCart;
        $this->reminderType = $reminderType;
        $this->discountCoupon = $discountCoupon;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->getSubject();

        // Use USER_URL and ensure locale is included
        $userUrl = env('USER_URL', 'http://localhost:8000/en');
        // Remove trailing slash and any existing locale
        $userUrl = rtrim($userUrl, '/');
        $userUrl = preg_replace('#/(en|ar)$#', '', $userUrl);
        // Add locale prefix (default to 'en') and cart path
        $locale = $this->abandonedCart->student->preferred_language ?? 'en';
        $cartUrl = $userUrl . '/' . $locale . '/cart';

        return $this->subject($subject)
                    ->view('emails.abandoned-cart-reminder')
                    ->with([
                        'student' => $this->abandonedCart->student,
                        'items' => $this->abandonedCart->items,
                        'totalAmount' => $this->abandonedCart->total_amount,
                        'reminderType' => $this->reminderType,
                        'discountCoupon' => $this->discountCoupon,
                        'cartUrl' => $cartUrl,
                    ]);
    }

    /**
     * Get email subject based on reminder type.
     */
    private function getSubject()
    {
        switch ($this->reminderType) {
            case 'second':
                return 'Don\'t miss out! Your cart is waiting';
            case 'third':
                return 'Last chance! Special discount inside';
            default:
                return 'You left something in your cart';
        }
    }
}
