<?php

namespace Rboschin\LaravelMailLog\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;
    public $subjectLine;

    public function __construct(array $payload, string $subjectLine)
    {
        $this->payload = $payload;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        $verbose = (bool) config('mail-log.is_verbose', true);

        return $this->subject($this->subjectLine)
                    ->text('laravel-mail-log::emails.log_plain')
                    ->with(['payload' => $this->payload, 'verbose' => $verbose]);
    }
}
