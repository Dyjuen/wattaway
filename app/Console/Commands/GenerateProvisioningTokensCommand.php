<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DeviceProvisioningToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateProvisioningTokensCommand extends Command
{
    protected $signature = 'tokens:generate 
                            {quantity : Number of tokens to generate}
                            {--batch= : Manufacturing batch number}
                            {--export : Export to CSV file}';

    protected $description = 'Generate provisioning tokens for manufacturing';

    public function handle(): int
    {
        $quantity = (int) $this->argument('quantity');
        $batch = $this->option('batch') ?? date('Ymd');
        $export = $this->option('export');

        $this->info("Generating {$quantity} tokens for batch {$batch}...");

        $bar = $this->output->createProgressBar($quantity);
        $tokens = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $serial = sprintf('WS%s%04d', $batch, $i);
            $hardwareId = 'ESP32-' . strtoupper(Str::random(12));

            $token = DeviceProvisioningToken::generate($serial, $hardwareId, [
                'batch' => $batch,
                'manufacturing_date' => now()->toDateString(),
            ]);

            if ($export) {
                $tokens[] = [
                    'Token' => $token->token,
                    'Serial Number' => $token->serial_number,
                    'Hardware ID' => $token->hardware_id,
                    'QR URL' => $token->getQrCodeUrl(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($export) {
            $filename = "provisioning_tokens_batch_{$batch}.csv";
            $path = "app/{$filename}";
            $file = fopen(storage_path($path), 'w');

            fputcsv($file, array_keys($tokens[0]));

            foreach ($tokens as $token) {
                fputcsv($file, $token);
            }

            fclose($file);

            $this->info("Exported tokens to {$filename}");
        }

        $this->info("Successfully generated {$quantity} tokens.");

        return Command::SUCCESS;
    }
}
