<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Combination;
use App\Jobs\GenerateLlmResponseJob;

class GenerateResponses extends Command
{
    protected $signature = 'generate:responses';
    protected $description = 'Generate responses for ungenerated combinations';

    public function handle()
    {
        $combinations = Combination::where('is_generated', false)->get();
        foreach ($combinations as $combination) {
            GenerateLlmResponseJob::dispatch($combination);
        }

        $this->info('Responses generation jobs dispatched.');
    }
}