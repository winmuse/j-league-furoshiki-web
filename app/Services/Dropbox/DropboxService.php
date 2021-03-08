<?php

namespace App\Services\Dropbox;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Media;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

/**
 * Class DropboxService
 * @package App\Http\Services\Dropbox
 */
class DropboxService
{
    /**
     * 情報の更新
     *
     * @param Admin $user
     * @param array $attributes
     */
    public function updateDropbox(Admin $user, array $attributes): void
    {
        $attributes = Arr::get($attributes, 'dropbox');

        $user->dropbox()->updateOrCreate([
            'admin_id' => $user->id
        ], $attributes);
    }

    /**
     * @param Admin $admin
     *
     */
    public function fetchDropbox(Admin $admin, $limit = -1)
    {
        try {
            $client = new Client($admin->dropbox->_token);

            $folder = $admin->dropbox->folder;
            $md = $client->listFolder($folder);
            if ($md) {
                $size = 0;
                foreach ($md['entries'] as $entry) {
                    if ($limit !== -1 && $size >= $limit) return;
                    // if (strpos($entry['name'], '.mp4') > 0 && $admin->role != 'j-league') return;

                    if (strpos($entry['name'], '.mp4') > 0 || strpos($entry['name'], '.jpg') > 0) {
                        // if (strpos($entry['name'], '.jpg') > 0) {
                        $entry['extension'] = strpos($entry['name'], '.mp4') > 0 ? 'mp4' : 'jpg';

                        $obj = Media::where('filename', $entry['path_display'])
                            ->where('extension', $entry['extension'])->first();

                        if ($obj) continue;

                        \DB::beginTransaction();
                        try {
                            $filenames = $this->getInfo($entry['name']);

                            $shared = $client->listSharedLinks($entry['path_display']);
                            foreach ($shared as $sharedItem) {
                                if ($sharedItem['.tag'] === 'file') {
                                    $shared = $sharedItem['url'];
                                    break;
                                }
                            }
                            if (!is_string($shared)) {
                                $client->createSharedLinkWithSettings($entry['path_display']);
                                $shared = $client->listSharedLinks($entry['path_display']);
                                foreach ($shared as $sharedItem) {
                                    if ($sharedItem['.tag'] === 'file') {
                                        $shared = $sharedItem['url'];
                                        break;
                                    }
                                }
                            }

                            $thumbnail = $client->getThumbnail($entry['path_display'], 'jpeg', 'w256h256');

                            $thumbPath = 'thumbnail/videos/dropbox/club_' . $admin->id . str_replace('.mp4', '.jpg', $entry['path_display']);
                            Storage::disk('s3_app')->put($thumbPath, $thumbnail);

                            $mediaAttributes = [
                                'creator' => $admin->name,
                                'filename' => $entry['path_display'],
                                'folder' => $shared,
                                'source' => Media::DROPBOX_SOURCE,
                                'uploaded_at' => Carbon::now(),
                                'extension' => $entry['extension'],
                                'club_id' => $admin->id
                            ];
                            $metaAttributes = [
                                'event' => $filenames[0],
                                'players' => sizeof($filenames) >= 2 ? $filenames[1] : '',
                                'home_team' => $admin->name
                            ];

                            $media = Media::create($mediaAttributes);
                            $media->meta()->updateOrCreate([
                                'media_id' => $media->id
                            ], $metaAttributes);

                            $size++;
                        } catch (\Throwable $e) {
                            //logger()->error($e->getMessage());
                            \DB::rollBack();
                        }

                        \DB::commit();
                    }
                }
            }
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            return "error";
        }
    }

    /**
     * @param Admin $admin
     *
     */
    public function fetchDropboxJleague(Admin $admin, $limit = -1)
    {
        try {
            $client = new Client($admin->dropbox->_token);

            $folder = $admin->dropbox->folder;
            $md = $client->listFolder($folder);
            if ($md) {
                $size = 0;
                foreach ($md['entries'] as $entry) {
                    if ($limit !== -1 && $size >= $limit) return;

                    if (strpos($entry['name'], '.mp4') > 0 || strpos($entry['name'], '.jpg') > 0) {
                        $entry['extension'] = strpos($entry['name'], '.mp4') > 0 ? 'mp4' : 'jpg';

                        $obj = Media::where('filename', $entry['path_display'])
                            ->where('extension', $entry['extension'])->first();

                        if ($obj) continue;

                        \DB::beginTransaction();
                        try {
                            $filenames = $this->getInfo($entry['name']);

                            // search club_id
                            $user = User::where(function ($q) use ($filenames) {
                                $q->orWhere('name', $filenames[1]);
                                $q->orWhere('name_en', $filenames[1]);
                            })->first();
                            $club = $user->profile->club;

                            $shared = $client->listSharedLinks($entry['path_display']);
                            foreach ($shared as $sharedItem) {
                                if ($sharedItem['.tag'] === 'file') {
                                    $shared = $sharedItem['url'];
                                    break;
                                }
                            }
                            if (!is_string($shared)) {
                                $client->createSharedLinkWithSettings($entry['path_display']);
                                $shared = $client->listSharedLinks($entry['path_display']);
                                foreach ($shared as $sharedItem) {
                                    if ($sharedItem['.tag'] === 'file') {
                                        $shared = $sharedItem['url'];
                                        break;
                                    }
                                }
                            }

                            $thumbnail = $client->getThumbnail($entry['path_display'], 'jpeg', 'w256h256');

                            $thumbPath = 'thumbnail/videos/dropbox/club_' . $club->id . str_replace('.mp4', '.jpg', $entry['path_display']);
                            Storage::disk('s3_app')->put($thumbPath, $thumbnail);

                            $mediaAttributes = [
                                'creator' => $club->name,
                                'filename' => $entry['path_display'],
                                'folder' => $shared,
                                'source' => Media::DROPBOX_SOURCE,
                                'uploaded_at' => Carbon::now(),
                                'extension' => $entry['extension'],
                                'club_id' => $club->id
                            ];
                            $metaAttributes = [
                                'event' => $filenames[0],
                                'players' => sizeof($filenames) >= 2 ? $filenames[1] : '',
                                'home_team' => $club->name
                            ];

                            $media = Media::create($mediaAttributes);
                            $media->meta()->updateOrCreate([
                                'media_id' => $media->id
                            ], $metaAttributes);

                            $size++;
                        } catch (\Throwable $e) {
                            logger()->error($e->getMessage());
                            \DB::rollBack();
                        }

                        \DB::commit();
                    }
                }
            }
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            return "error";
        }
    }

    /**
     * ファイル名は以下の2パターンを想定
     * 1. Goal by Kazunari Ichimi_e8c96b05-1fcb-4aac-bb07-65b6cfdec411.mp4
     * 2. Leandro Pereira with a Goal vs. Vissel Kobe_616e1fd…472bcf3c8d.mp4
     *
     * @param $filename
     */
    protected function getInfo($filename)
    {
        // pattern 1
        $filenames = explode(' by ', explode('_', $filename)[0]);
        // pattern 2
        if(count($filenames) < 2) {
            $filenames = array_reverse(explode(' with ', explode('_', $filename)[0]));
        }
        return $filenames;
    }

    /**
     * @param Admin $admin
     * @param int $lastDays
     */
    public function deleteOldData(Admin $admin, int $lastDays)
    {
        $lastDay = Carbon::now()->addDays($lastDays)->format('Y-m-d 23:59:59');

        $medias = Media::where('uploaded_at', '<=', $lastDay)
            ->where('source', Media::DROPBOX_SOURCE)->get();
        foreach ($medias as $media) {
            try {
                $thumbpath = 'thumbnail/videos/dropbox/club_' . $media->club_id . str_replace('.mp4', '.jpg', $media->filename);

                \App\Jobs\DeleteMediaJob::dispatch($media->source, $media->filename, $admin->dropbox->_token, $thumbpath);
                $media->delete();
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * Test Dropbox Token & Folder
     *
     * @param array $attributes
     *
     * @return boolean
     */
    public function testDropbox(array $attributes)
    {
        $attributes = Arr::get($attributes, 'dropbox');

        $client = new Client($attributes['_token']);

        try {
            $md = $client->listFolder($attributes['folder']);
            if ($md) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }
}
