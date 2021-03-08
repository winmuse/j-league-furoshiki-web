<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Dropbox\DropboxService;
use App\Models\Admin;

class DeleteDropbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:dropbox {days?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old data from Dropbox';

    /**
     * @var DropboxService
     */
    private $service;

    /**
     * Create a new command instance.
     *
     * @param DropboxService $service
     *
     * @return void
     */
    public function __construct(
        DropboxService $service
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
        $clubs = Admin::where(function ($q) {
            $q->orWhere('role', Admin::CLUB_ROLE);
            $q->orWhere('role', Admin::JLEAGUE_ROLE);
            })->whereHas('dropbox')->get();

        foreach ($clubs as $club) {
            $this->service->deleteOldData($club, $days);
        }

        $this->info('success');
    }
}
