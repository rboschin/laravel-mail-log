<?php

namespace Rboschin\LaravelMailLog\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MailLogStatus extends Command
{
    protected $signature = 'mail-log:status';
    protected $description = 'Show mail-log package status and effective configuration';

    public function handle()
    {
        $cfg = config('mail-log', []);

        $this->info('Mail Log configuration:');
        $this->line("  enabled: " . ($cfg['enabled'] ? 'yes' : 'no'));
        $this->line("  to: " . ($cfg['to'] ?? '<not set>'));
        $this->line("  from: " . ($cfg['from'] ?? '<not set>'));
        $this->line("  levels: " . implode(',', $cfg['levels'] ?? []));
        $envs = $cfg['environments'] ?? [];
        $this->line("  environments: " . (empty($envs) ? '<all>' : implode(',', $envs)));
        $this->line("  auto_register: " . (!empty($cfg['auto_register']) ? 'yes' : 'no'));
        $this->line("  queue: " . (!empty($cfg['queue']) ? 'yes' : 'no'));
        $this->line("  is_verbose: " . (!empty($cfg['is_verbose']) ? 'yes' : 'no'));

        // Determine if sending is effectively enabled given current env
        $effective = false;
        $reasons = [];

        if (empty($cfg['enabled'])) {
            $reasons[] = 'disabled (MAIL_LOG_ENABLED is false)';
        }

        if (empty($cfg['to'])) {
            $reasons[] = 'no recipient set (MAIL_LOG_TO)';
        }

        if (empty($cfg['auto_register'])) {
            $reasons[] = 'auto_register disabled';
        }

        $envs = $cfg['environments'] ?? [];
        if (!empty($envs) && is_array($envs) && !in_array(app()->environment(), $envs, true)) {
            $reasons[] = 'current environment ('.app()->environment().') is not in configured environments';
        }

        if (empty($reasons)) {
            $effective = true;
        }

        $this->line('');
        $this->info('Effective sending: ' . ($effective ? 'ENABLED' : 'DISABLED'));
        if (!$effective) {
            $this->line('  Reasons:');
            foreach ($reasons as $r) {
                $this->line("    - $r");
            }
        }

        // Check handler presence
        $monolog = Log::getLogger();
        $handlers = method_exists($monolog, 'getHandlers') ? $monolog->getHandlers() : [];
        $found = false;
        foreach ($handlers as $h) {
            if (is_object($h) && (get_class($h) === \Rboschin\LaravelMailLog\Logging\MailLogHandler::class || strpos(get_class($h), 'MailLogHandler') !== false)) {
                $found = true;
                break;
            }
        }

        $this->line('Handler registered: ' . ($found ? 'yes' : 'no'));

        return $found ? 0 : 1;
    }
}
