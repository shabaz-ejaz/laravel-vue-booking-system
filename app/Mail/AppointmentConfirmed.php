<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use Illuminate\Mail\Mailables\Address;
class AppointmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Appointment $appointment,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
             from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Appointment Confirmed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment.confirmed',
            with: [
                'name' => $this->appointment->name,
                'vehicle_make' => $this->appointment->vehicle_make,
                'vehicle_model' => $this->appointment->vehicle_model,
                'date' => Carbon::parse($this->appointment->date)->format('d-m-Y'),
                'start_at' => Carbon::createFromFormat('H:i:s', $this->appointment->start_at)->format('H:i:s')
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
