<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\HandlesStudentName;

class AdminNewPurchase extends Mailable
{
    use Queueable, SerializesModels, HandlesStudentName;

    public $order;
    public $student;
    public $orderDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->student = $order->student;

        // Prepare order details
        $this->orderDetails = [
            'order_id' => $order->id,
            'order_date' => $order->created_at->format('F d, Y H:i'),
            'student_name' => $this->student ? $this->getStudentName($this->student) : 'Unknown Student',
            'student_email' => $this->student->email ?? 'N/A',
            'total_amount' => number_format($order->total_price ?? 0, 2),
            'currency' => $order->currency ?? 'USD',
            'payment_status' => $order->status ?? 'pending',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Purchase - Order #' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-purchase',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
