<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\Dropbox\Client;

class DeleteMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $source;
    protected $filepath;
    protected $_token;
    protected $thumbpath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($source, $filepath, $_token = '', $thumbpath = '')
    {
        $this->source = $source;
        $this->filepath = $filepath;
        $this->_token = $_token;
        $this->thumbpath = $thumbpath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->source === \App\Models\Media::AWS_SOURCE) {
            Storage::disk('s3_app')->delete('thumbnail/photos/' . $this->filepath . '.jpg');
            Storage::disk('s3')->delete($this->filepath . '.jpg');
            Storage::disk('s3')->delete($this->filepath . '.json');
        }

        if ($this->source === \App\Models\Media::DROPBOX_SOURCE) {
            try {
                Storage::disk('s3_app')->delete($this->thumbpath);
            } catch (\Throwable $e) {
            }

            $client  = new Client($this->_token);
            $client->delete($this->filepath);
        }
    }
}
