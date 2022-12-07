<?php

namespace App\Console\Commands;

use App\Jobs\FetchDashboardJob;
use Illuminate\Console\Command;

class UpdateDashboardDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:update-data {dashboardType?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data dashboard';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->argument('dashboardType')) {
            if (in_array($this->argument('dashboardType'), ['statistic'])) {
                dispatch(new FetchDashboardJob($this->argument('dashboardType')));
                return 0;
            }
        }
        dispatch(new FetchDashboardJob(null));
        return 0;
    }
}
