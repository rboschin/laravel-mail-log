<?php

namespace Rboschin\LaravelMailLog\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Rboschin\LaravelMailLog\Mail\LogMail;

class QueueSendLogMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function handle()
    {
        $m = new LogMail($this->mailData['payload'], $this->mailData['subject']);
        if (!empty($this->mailData['from'])) {
            $m->from($this->mailData['from']);
        }

        Mail::to($this->mailData['to'])->send($m);
    }
}
