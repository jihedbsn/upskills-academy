<?php

namespace App\Console\Commands;

use App\Services\EmailInboxService;
use Illuminate\Console\Command;

class FetchInboundEmailsCommand extends Command
{
    protected $signature = 'emails:fetch-inbound';

    protected $description = 'Import unread inbound emails from the configured IMAP mailbox';

    public function handle(EmailInboxService $inboxService): int
    {
        try {
            $count = $inboxService->syncFromImap();
            $this->info("Imported {$count} inbound email(s).");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
