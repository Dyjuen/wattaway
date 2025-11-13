<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class EnsureApiTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:ensure-api-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all accounts have a unique API token.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for accounts without API tokens...');

        $accountsWithoutToken = Account::whereNull('api_token')->orWhere('api_token', '')->get();

        if ($accountsWithoutToken->isEmpty()) {
            $this->info('All accounts already have an API token.');
            return 0;
        }

        $this->warn("Found " . $accountsWithoutToken->count() . " accounts without an API token. Generating tokens now...");

        foreach ($accountsWithoutToken as $account) {
            $account->api_token = $this->generateUniqueApiToken();
            $account->save();
            $this->line("Generated token for account: {$account->email}");
        }

        $this->info('Successfully generated missing API tokens.');
        return 0;
    }

    /**
     * Generate a unique API token.
     *
     * @return string
     */
    private function generateUniqueApiToken()
    {
        do {
            $token = Str::random(60);
        } while (Account::where('api_token', $token)->exists());

        return $token;
    }
}
