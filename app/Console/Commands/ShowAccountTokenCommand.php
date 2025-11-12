<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

class ShowAccountTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wattaway:show-account-token {account_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the API token for a specific account.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accountId = $this->argument('account_id');
        $account = Account::find($accountId);

        if (!$account) {
            $this->error("Account with ID {$accountId} not found.");
            return 1;
        }

        if ($account->api_token) {
            $this->info("API Token for account {$accountId}:");
            $this->line($account->api_token);
        } else {
            $this->warn("Account {$accountId} does not have an API token.");
        }

        return 0;
    }
}
