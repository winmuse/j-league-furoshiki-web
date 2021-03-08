<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Dropbox\DropboxService;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;

class FetchDropboxJleague extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:dropbox:jleague {limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from Dropbox';

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
        $limit = $this->argument('limit') ? intval($this->argument('limit')) : -1;

        $clubs = Admin::where('role', Admin::JLEAGUE_ROLE)
                      ->whereHas('dropbox')->get();

        foreach ($clubs as $club) {
            $this->service->fetchDropboxJleague($club, $limit);
        }

        $this->info('success');
    }
}
