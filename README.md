
# Laravel-mail-log

Small Laravel package that sends log records via email.

## Quick install

In your app `composer.json` add a repository entry:

```json
"repositories": [
  {
      "type": "vcs",
      "url":  "https://github.com/rboschin/laravel-mail-log.git"
  }
]
```

Then require it:

```bash
composer require rboschin/laravel-mail-log:dev-main
```

## After install
- Publish config and views (optional):
  - php artisan vendor:publish --provider="Rboschin\\LaravelMailLog\\Providers\\MailLogServiceProvider" --tag=mail-log-config
  - php artisan vendor:publish --provider="Rboschin\\LaravelMailLog\\Providers\\MailLogServiceProvider" --tag=mail-log-views
- Configure `.env` (see `config/mail-log.php` keys or `.env.example`)
- Update configuration `php artisan config:clear`
- Start queue worker if `MAIL_LOG_QUEUE=true` (`php artisan queue:work`)

## Test
Use Tinker or trigger a log:

```bash
php artisan tinker
>>> \Illuminate\Support\Facades\Log::error('Test from mail-log package')
```

## Utility commands
```bash
php artisan mail-log:status
php artisan mail-log:send-test
php artisan mail-log:send-test --sync
```
