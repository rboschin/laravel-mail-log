<?php

namespace Rboschin\LaravelMailLog\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Rboschin\LaravelMailLog\Jobs\QueueSendLogMail;

class MailLogHandler extends AbstractProcessingHandler
{
    public function __construct($level = null, bool $bubble = true)
    {
        parent::__construct($level ?? config('logging.level', 'error'), $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $allowed = array_map('strtolower', array_map('trim', config('mail-log.levels', ['error'])));
        $levelName = strtolower($record->level->getName() ?? (string) $record->level);
        if (!in_array($levelName, $allowed, true)) {
            return;
        }

        $payload = [
            'message' => $record->message ?? '',
            'context' => $record->context ?? [],
            'level' => $record->level->getName() ?? (string) $record->level,
            'datetime' => $record->datetime->format('c'),
            'extra' => $record->extra ?? [],
            'app_env' => app()->environment(),
            'php_sapi' => PHP_SAPI,
            'pid' => getmypid(),
            'hostname' => gethostname(),
        ];

        if (PHP_SAPI === 'cli' && isset($_SERVER['argv'])) {
            $payload['cli'] = $_SERVER['argv'];
        }

        try {
            if (function_exists('request') && request()->hasSession()) {
                $payload['request'] = [
                    'method' => request()->method(),
                    'path' => request()->path(),
                    'ip' => request()->ip(),
                ];
            }
        } catch (\Throwable $_) {
        }

        $mailData = [
            'to' => config('mail-log.to'),
            'from' => config('mail-log.from'),
            'subject' => strtoupper($payload['level']) . ': ' . substr($payload['message'], 0, 120),
            'body' => substr($payload['message'], 0, 1000),
            'payload' => $payload,
        ];

        if (config('mail-log.queue', true)) {
            QueueSendLogMail::dispatch($mailData);
        } else {
            QueueSendLogMail::dispatchSync($mailData);
        }
    }
}
