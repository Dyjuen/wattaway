<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class EnsureAccountTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wattaway:ensure-account-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all accounts have an API token, generating one if missing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for accounts missing API tokens...');

        $accountsToFix = Account::whereNull('api_token')->get();

        if ($accountsToFix->isEmpty()) {
            $this->info('All accounts already have an API token. No action needed.');
            return 0;
        }

        $this->info("Found {$accountsToFix->count()} accounts to update.");

        foreach ($accountsToFix as $account) {
            $account->api_token = $this->generateUniqueApiToken();
            $account->save();
            $this->line("Updated token for account ID: {$account->id}");
        }

        $this->info('Successfully updated all accounts.');
        return 0;
    }

    private function generateUniqueApiToken()
    {
        do {
            $token = Str::random(60);
        } while (Account::where('api_token', $token)->exists());

        return $token;
    }
}
