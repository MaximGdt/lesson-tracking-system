<?php

namespace App\Console\Commands;

use App\Services\ExternalApiService;
use Illuminate\Console\Command;

class SyncHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holidays:sync {year? : The year to sync holidays for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Ukrainian holidays from external API';

    protected ExternalApiService $apiService;

    /**
     * Create a new command instance.
     */
    public function __construct(ExternalApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = $this->argument('year') ?? now()->year;
        
        $this->info("Syncing holidays for year: {$year}");
        
        try {
            $holidays = $this->apiService->getHolidays($year);
            
            if (empty($holidays)) {
                $this->warn('No holidays found from API.');
                return Command::FAILURE;
            }
            
            foreach ($holidays as $holidayData) {
                \App\Models\Holiday::updateOrCreate(
                    ['date' => $holidayData['date']],
                    $holidayData
                );
            }
            
            $this->info('Synced ' . count($holidays) . ' holidays successfully!');
            
            // Also sync next year if we're in the last quarter
            if (now()->quarter === 4) {
                $this->call('holidays:sync', ['year' => $year + 1]);
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync holidays: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}