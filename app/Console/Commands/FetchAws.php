<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use App\Services\AWS\AWSService;
use Illuminate\Support\Facades\Storage;

class FetchAws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:aws {directory?} {size?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from AWS S3';

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
     * S3から画像を取得する
     * [コマンド引数]
     *  directory : S3ディレクトリのindex
     *              indexを指定することにより取得するディレクトリを指定する
     *  sizes : 読み込むディレクトリ内のファイル数
     * @return mixed
     */
    public function handle()
    {
        $directoryIndex = $this->argument('directory') ? intval($this->argument('directory')) - 1 : -1;
        $fileSize = $this->argument('size') ? intval($this->argument('size')) : -1;

        $directories = Storage::disk('s3')->allDirectories('./');

        // temp
        if ($directoryIndex >= 0) {
            if ($directoryIndex >= sizeof($directories)) {
                $this->error('There are total ' . sizeof($directories) . ' in AWS');
                return;
            }
            
            $directories = [$directories[$directoryIndex]];
        }

        $success = true;

        foreach ($directories as $directory) {
            $files = Storage::disk('s3')->files($directory);
            $num = 0;
            
            if ($this->service->isNewDirectory($directory, sizeof($files) / 2)) {
                foreach ($files as $file) {
                    if ($fileSize > 0 && $num >= $fileSize) continue;

                    // JSONファイルの読み込み
                    if (strpos($file, '.json') > 0) {
                        /** @var Media $media */
                        $media = $this->service->fetchMediaIfExist($file);

                        \DB::beginTransaction();
                        try {
                            // 画像アップロード
                            $this->service->uploadPhoto(str_replace('.json', '.jpg', $file));

                            // mediaレコード登録or更新
                            $attributes = $this->service->makeMediaAttributesFromJsonFile($file);

                            $this->executeMediasDbProcess($media, $attributes);
                        } catch (\Throwable $e) {
                            //logger()->error($e->getMessage());
                            \DB::rollBack();
                            
                            $this->error('error occured on ' . $file);
                            $success = false;
                        }

                        if ($success) {
                            \DB::commit();

                            $this->info('success on ' . $file);
                        }

                        $num++;
                    }
                }
            }
        }

        $this->service->assignClubIDsToMediasHavingNoClubId();

        $this->info($success ? 'success' : 'failure');
    }

    /**
     * mediasに対してのDBプロセスを実行
     * @param Media|bool $media
     * @param array $attributes
     */
    private function executeMediasDbProcess($media, array $attributes)
    {
        $noPlayerName = empty($attributes['media_metas']['players']);

        // 新規登録
        if (!$media) {
            if ($noPlayerName) {
                $attributes['medias']['is_done'] = Media::NOT_DONE;
                $attributes['medias']['is_show'] = Media::SHOW;
            } else {
                $attributes['medias']['is_done'] = Media::DONE;
                $attributes['medias']['is_show'] = Media::SHOW;
            }

            $this->service->registerMedia($attributes);
            return;
        }

        // 更新
        if ($media->is_done === Media::DONE) {
            // すでに作業が完了しているレコードの場合

            // 新データに選手名無し
            // → 完了ステータスはそのまま、mediaデータ更新なし
            if ($noPlayerName) {
                return;
            }

            // 新データに選手名有り
            // → 完了ステータスはそのまま、mediaデータ更新あり
            //   さらに既存の選手名に対して、新たに選手名を追記する
            $attributes['medias']['is_done'] = $media->is_done;
            $attributes['medias']['is_show'] = $media->is_show;
        } else {
            // 作業が未完了のレコードの場合
            // → レコード更新

            if ($noPlayerName) {
                // 新データに選手名無し
                // → 未完了/公開
                $attributes['medias']['is_done'] = Media::NOT_DONE;
                $attributes['medias']['is_show'] = Media::SHOW;
            } else {
                // 新データに選手名有り
                // → 未完了/公開
                $attributes['medias']['is_done'] = Media::NOT_DONE;
                $attributes['medias']['is_show'] = Media::SHOW;
            }
        }

        // 選手名を追記
        $players = $media->meta->players;
        foreach (explode(',', $attributes['media_metas']['players']) as $player) {
            if ($player !== '' && strpos($players, $player) === false) {
                $players = $players . ',' . $player;
            }
        }
        $attributes['media_metas']['players'] = $players;

        $this->service->updateMedia($media, $attributes);
    }
}
