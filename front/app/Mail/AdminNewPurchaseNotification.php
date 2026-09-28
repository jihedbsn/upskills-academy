<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AdminNewPurchaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $orders;
    protected $student_model;
    public $orderDetails;
    public $items;
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct($orders)
    {
        // Accept single order or array of order IDs
        if ($orders instanceof Order) {
            $this->orders = collect([$orders]);
            $this->order = $orders;
        } elseif (is_array($orders)) {
            $this->orders = Order::with(['product', 'book', 'package', 'student'])->whereIn('id', $orders)->get();
            $this->order = $this->orders->first();
        } else {
            $this->orders = collect([$orders]);
            $this->order = $orders;
        }

        $firstOrder = $this->orders->first();
        $this->student_model = $firstOrder->student;

        // Calculate total amount
        $totalAmount = $this->orders->sum('price');

        // Prepare order details
        $this->orderDetails = [
            'order_id' => $firstOrder->id,
            'order_date' => $firstOrder->created_at->format('F d, Y H:i'),
            'student_name' => $this->student_model ? ($this->student_model->first_name . ' ' . $this->student_model->last_name) : 'Unknown Student',
            'student_email' => $this->student_model->email ?? 'N/A',
            'total_amount' => $totalAmount,
            'currency' => $firstOrder->currency ?? 'USD',
            'payment_status' => $firstOrder->payment_status ?? $firstOrder->status ?? 'pending',
            'payment_method' => $firstOrder->payment_method ?? 'N/A',
        ];

        // Prepare items list
        $this->items = $this->orders->map(function($order) {
            $title = 'Unknown Item';
            $type = 'item';

            if ($order->product) {
                $title = $order->product->titre ?? $order->product->name_en ?? $order->product->name_ar ?? 'Course';
                $type = 'course';
            } elseif ($order->book) {
                $title = $order->book->titre ?? $order->book->title ?? 'Book';
                $type = 'book';
            } elseif ($order->package) {
                $title = $order->package->title ?? 'Package';
                $type = 'package';
            }

            return [
                'order_id' => $order->id,
                'title' => $title,
                'type' => ucfirst($type),
                'quantity' => $order->quantity ?? 1,
                'price' => $order->price ?? 0,
            ];
        })->toArray();
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
            with: [
                'orderId' => $this->orderDetails['order_id'],
                'customerName' => $this->orderDetails['student_name'],
                'customerEmail' => $this->orderDetails['student_email'],
                'orderDate' => $this->orderDetails['order_date'],
                'totalAmount' => $this->orderDetails['total_amount'],
                'paymentStatus' => ucfirst($this->orderDetails['payment_status']),
                'paymentMethod' => ucfirst(str_replace('_', ' ', $this->orderDetails['payment_method'])),
                'items' => $this->items,
                'orderUrl' => env('ADMIN_URL', config('app.admin_url')) . '/admin/orders/' . $this->orderDetails['order_id'],
                'order' => $this->order,
                'student' => $this->student_model,
                'orderDetails' => $this->orderDetails,
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
