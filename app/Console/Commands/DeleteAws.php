<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AWS\AWSService;

class DeleteAws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:aws {days?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old data from AWS';

    /**
     * @var AWSService
     */
    private $service;

    /**
     * Create a new command instance.
     * 
     * @param AWSService $service
     *
     * @return void
     */
    public function __construct(
        AWSService $service
    )
    {
        $this->service = $service;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = $this->argument('days') ? (0 - intval($this->argument('days'))) : -30;

        $this->service->deleteOldData($days);
    }
}
