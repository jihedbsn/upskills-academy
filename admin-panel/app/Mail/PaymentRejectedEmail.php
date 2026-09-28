<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRejectedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $student;
    public $rejectionComment;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Student $student, $rejectionComment = null)
    {
        $this->order = $order;
        $this->student = $student;
        $this->rejectionComment = $rejectionComment ?? $order->rejection_comment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Rejected - Order #' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-rejected',
            with: [
                'studentName' => $this->student->first_name . ' ' . $this->student->last_name,
                'orderId' => $this->order->id,
                'amount' => number_format($this->order->price, 2),
                'paymentMethod' => ucwords(str_replace('_', ' ', $this->order->payment_method)),
                'orderDate' => $this->order->created_at->format('F d, Y H:i'),
                'rejectionComment' => $this->rejectionComment,
            ],
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
