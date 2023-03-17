<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $people;
    protected $events;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($people)
    {
        $this->people = $people;
        $this->events = Event::whereHas('peoples', function ($query) use ($people) {
            $query->where('attended', false);
            $query->where('people_id', '=', $people->id);
        })->get();
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Training Schedule',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'email',
            with: [
                'people' => $this->people,
                'events' => $this->events
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
