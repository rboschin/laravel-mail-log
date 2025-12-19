<?php

namespace Rboschin\LaravelMailLog\Console\Commands;

use Illuminate\Console\Command;
use Rboschin\LaravelMailLog\Jobs\QueueSendLogMail;

class MailLogSendTest extends Command
{
    protected $signature = 'mail-log:send-test {--to=} {--subject=Mail log test} {--message=Test message} {--sync}';
    protected $description = 'Send a test log email via the mail-log pipeline';

    public function handle()
    {
        $to = $this->option('to') ?: config('mail-log.to');
        $from = config('mail-log.from');
        $subject = $this->option('subject');
        $message = $this->option('message');

        if (empty($to)) {
            $this->error('No recipient set. Use --to or set MAIL_LOG_TO in your .env');
            return 1;
        }

        $payload = [
            'message' => $message,
            'datetime' => now()->toIsoString(),
            'level' => 'error',
            'app_env' => app()->environment(),
        ];

        $mailData = [
            'to' => $to,
            'from' => $from,
            'subject' => $subject,
            'payload' => $payload,
        ];

        try {
            if ($this->option('sync')) {
                QueueSendLogMail::dispatchSync($mailData);
            } else {
                QueueSendLogMail::dispatch($mailData);
            }
            $this->info('Test mail queued/sent to ' . $to);
            return 0;
        } catch (\Throwable $e) {
            $this->error('Failed to send test mail: ' . $e->getMessage());
            return 2;
        }
    }
}
