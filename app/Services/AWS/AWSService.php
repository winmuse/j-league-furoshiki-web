<?php

namespace App\Services\AWS;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Media;
use App\Models\Admin;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class AWSService
 * @package App\Http\Services\AWS
 */
class AWSService
{
    /** @var int */
    private const PER_PAGE = 20;

    /**
     * @param array $attributes
     *
     * @return LengthAwarePaginator
     */
    public function search(array $attributes): LengthAwarePaginator
    {
        $query = $this->getQuery($attributes);
        return $query->paginate(static::PER_PAGE);
    }

    /**
     * @param Builder $query
     * @param array   $attributes
     * @param string  $column
     *
     * @return Builder
     */
    private function scopeLikeBuilder(Builder $query, array $attributes, string $column): Builder
    {
        if (empty($value = Arr::get($attributes, $column))) {
            return $query;
        }
        $value = str_replace("　", ' ', $value);

        return $query->where($column, 'like', "%{$value}%");
    }

    /**
     * @param Builder $query
     * @param array   $attributes
     * @param string  $column
     *
     * @return Builder
     */
    private function scopeMetaLikeBuilder(Builder $query, array $attributes, string $column): Builder
    {
        if (empty($value = Arr::get($attributes, $column))) {
            return $query;
        }

        $value = str_replace("　", ' ', $value);

        return $query->whereIn('id', function ($subquery) use ($value, $column) {
            $subquery->select('media_id')
                ->from(with(new \App\Models\MediaAWSMeta)->getTable())
                ->where($column, 'like', "%{$value}%");
        });
    }

    /**
     * @param Builder $query
     * @param array   $attributes
     * @param string  $column
     *
     * @return Builder
     */
    private function scopeClubLikeBuilder(Builder $query, array $attributes, string $column): Builder
    {
        if (empty($value = Arr::get($attributes, $column))) {
            return $query;
        }

        $value = str_replace("　", ' ', $value);

        return $query->whereIn('club_id', function ($subquery) use ($value, $column) {
            $subquery->select('id')
                ->from(with(new \App\Models\Admin)->getTable())
                ->where($column, 'like', "%{$value}%");
        });
    }

    /**
     * @param Builder $query
     * @param array   $attributes
     * @param string  $column
     *
     * @return Builder
     */
    private function scopeEqualBuilder(Builder $query, array $attributes, string $column): Builder
    {
        if (is_null($value = Arr::get($attributes, $column))) {
            return $query;
        }

        return $query->where($column, $value);
    }

    /**
     * @param string $filepath
     * @return Media|boolean
     */
    public function fetchMediaIfExist(string $filepath)
    {
        $files = explode('/', $filepath);
        $folder = $files[0];
        $filename = explode('.', $files[1])[0];

        $media = Media::where("filename", $filename)
                      ->where("folder", $folder)->first();

        return $media ?? false;
    }

    public function isNewDirectory(string $directory, $size)
    {
        $count = Media::where('folder', $directory)->count();

        return $count !== $size;
    }

    /**
     * @param $filepath
     * @return string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function uploadPhoto($filepath)
    {
        $size = 300;

        $thumbnail = Image::make( Storage::disk('s3')->get($filepath) );

        if ($thumbnail->width() < $size || $thumbnail->height() < $size) {
            $thumbnail = $thumbnail->resize($thumbnail->width(), $thumbnail->height())->stream();
        } else {
            if ($thumbnail->width() > $thumbnail->height()) {
                $width = intval(doubleval($thumbnail->width()) / (doubleval($thumbnail->height()) / doubleval($size)));
                $thumbnail = $thumbnail->resize($width, $size)->stream();
            } else {
                $height = intval(doubleval($thumbnail->height()) / (doubleval($thumbnail->width()) / doubleval($size)));
                $thumbnail = $thumbnail->resize($size, $height)->stream();
            }
        }

        $thumbPath = 'thumbnail/photos/' . $filepath;
        Storage::disk('s3_app')->put($thumbPath, $thumbnail);

        return ['thumb_url' => config('values.AWS_S3_URL_FOR_APP') . '/' . $thumbPath];
    }

    /**
     * @param $filepath
     * @return array[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function makeMediaAttributesFromJsonFile($filepath)
    {
        $files = explode('/', $filepath);
        $folder = $files[0];
        $filename = explode('.', $files[1])[0];
        $data = json_decode(Storage::disk('s3')->get($filepath), true);

        $mediaAttributes = [
            'creator' => $data['クレジット'] ? $data['クレジット'] : '',
            'filename' => $filename,
            'folder' => $folder,
            'source' => Media::AWS_SOURCE,
            'uploaded_at' => Carbon::now(),
            'extension' => 'jpg',
        ];
        $metaAttributes = [
            'event' => $data['イベント名'],
            'game' => $data['試合名'],
            'game_date' => $data['撮影日'],
            'game_place' => $data['撮影場所'],
            'game_time' => $data['昼／夜'],
            'home_team' => $data['チーム名(ホーム)'],
            'away_team' => $data['チーム名(アウェイ)'],
            'players' => $data['選手名'],
            'subject1' => $data['被写体 1'],
            'subject2' => $data['被写体 2'],
            'subject3' => $data['被写体 3'],
            'state1' => $data['状態 1'],
            'state2' => $data['状態 2'],
            'state3' => $data['状態 3'],
            'group_name' => $data['グループ']
        ];

        return [
            'medias' => $mediaAttributes,
            'media_metas' => $metaAttributes,
        ];
    }

    /**
     * club idを持たないmediasレコードに対して, クラブ名が一致するadminsレコードのid(= club id)を割り当てる
     */
    public function assignClubIDsToMediasHavingNoClubId()
    {
        $medias = Media::where('source', Media::AWS_SOURCE)
                       ->whereNull('club_id')->get();

        foreach ($medias as $media) {
            if (isset($media->meta)) {
                $teams = explode(';', $media->meta->home_team);
                $team = $teams[sizeof($teams) - 1];
                $admin = Admin::where('name', $team)
                              ->orWhereIn('name', function ($query) use ($team) {
                                    $query->select('name')
                                        ->from(with(new \App\Models\AdminMeta)->getTable())
                                        ->where('mapped_name', $team);
                              })->first();

                if (isset($admin)) {
                    $media->club_id = $admin->id;
                    $media->save();
                }
            }
        }
    }

    /**
     * @param int $lastDays
     */
    public function deleteOldData(int $lastDays)
    {
        $lastDay = Carbon::now()->addDays($lastDays)->format('Y-m-d 23:59:59');

        $medias = Media::where('uploaded_at', '<=', $lastDay)
                       ->where('source', Media::AWS_SOURCE)->get();
        foreach ($medias as $media) {
            $filepath = $media->folder . '/' . $media->filename;

            \App\Jobs\DeleteMediaJob::dispatch($media->source, $media->folder . '/' . $media->filename);
            $media->delete();
        }
    }

    /**
     * Delete
     *
     * @param int $id
     */
    public function deleteMedia(int $id): void
    {
        $media = Media::find($id);

        \App\Jobs\DeleteMediaJob::dispatch($media->source, $media->folder . '/' . $media->filename);

        Media::find($id)->delete();
    }

    /**
     * 表示／非表示
     *
     * @param int $id
     */
    public function toggleMedia(int $id): void
    {
        $media = Media::find($id);

        $media->is_show = $media->is_show === 1 ? 0 : 1;
        $media->save();
    }

    /**
     * 素材を取得する
     *
     * @param int $id
     *
     * @return Media
     */
    public function getMedia(int $id): Media
    {
        return Media::with('meta')->find($id);
    }

    /**
     * 素材の登録
     *
     * @param $attributes
     */
    public function registerMedia($attributes): void
    {
        $mediaAttributes = Arr::get($attributes, 'medias');
        $metaAttributes = Arr::get($attributes, 'media_metas');

        $media = Media::create($mediaAttributes);
        $media->meta()->updateOrCreate([
            'media_id' => $media->id
        ], $metaAttributes);
    }

    /**
     * 素材の更新
     *
     * @param Media $media
     * @param array $attributes
     */
    public function updateMedia(Media $media, array $attributes): void
    {
        $mediaAttributes = Arr::get($attributes, 'medias');
        $metaAttributes = Arr::get($attributes, 'media_metas');

        $mediaAttributes['updated_at'] = Carbon::now();

        $media->update($mediaAttributes);
        $media->meta()->updateOrCreate([
            'media_id' => $media->id
        ], $metaAttributes);
    }

    /**
     * @param array $attributes
     *
     */
    public function searchForExport(array $attributes)
    {
        $query = $this->getQuery($attributes);

        return $query->get();
    }

    private function getQuery(array $attributes)
    {
        $query = \DB::table('medias as m')
                  ->leftJoin('medias_aws_meta as t', 'm.id', '=', 't.media_id')
                  ->leftJoin('admins as a', 'm.club_id', '=', 'a.id');
//                  ->where(function ($query) {
//                      $query->where('m.extension', 'jpg')
//                        ->orWhere(function($q) {
//                            $q->where('m.extension', 'mp4')
//                              ->where('m.is_done', 1);
//                        });
//                  });

        if (!is_null($value = Arr::get($attributes, 'extension'))) {
            $value = str_replace("　", ' ', $value);
            $query = $query->where('m.extension', 'like', "%{$value}%");
        }

        if (!is_null($value = Arr::get($attributes, 'is_done'))) {
            $query = $query->where('m.is_done', $value);
        }

        if (!is_null($value = Arr::get($attributes, 'event'))) {
            $value = str_replace("　", ' ', $value);
            $query = $query->where('t.event', 'like', "%{$value}%");
        }

        if (!is_null($value = Arr::get($attributes, 'movie_type'))) {
            $query = $query->where('t.event', 'like', "%{$value}%");
        }

        // クラブ名が指定されていて、選手名が「すべて」の場合
        if (!is_null($value = Arr::get($attributes, 'name')) && is_null(Arr::get($attributes, 'players'))) {
            if (config('app.env') === 'stg') {
                // STGのみ選手名にクラブ名が含まれていなくても表示する
                // ただしクラブ名で絞り込みを行うために、クラブ名に素材のcreatorが一致したものを取得する
                $query->where('m.creator', Auth::user()->name);
            } else {
                // クラブ所属の選手を全て取得
                $teamMemberUserIds = Profile::where('admin_id', $value)->get()->pluck('user_id');
                $teamMemberUsers = User::query()->whereIn('id', $teamMemberUserIds)->get();

                // メンバーが登録されていない場合はデータを取得しない
                if (count($teamMemberUsers) === 0) {
                    $query = $query->where([
                                               ['t.players', '<>', ''],
                                               ['t.players', '=', ''],
                                           ]);
                }

                // 選手名がplayersに含まれる
                $query->where(function($query) use ($teamMemberUsers) {
                    foreach ($teamMemberUsers as $teamMemberUser) {
                        $query = $query->orWhereRaw("FIND_IN_SET('{$teamMemberUser->name}', t.players)");
                        $query = $query->orWhereRaw("FIND_IN_SET('{$teamMemberUser->name_en}', t.players)");
                    }
                });
            }
        }

        if (!is_null($value = Arr::get($attributes, 'players'))) {
            $name = User::where('id', $value)->first()->name;
            $name = str_replace("　", '', $name);
            $query = $query->whereRaw("FIND_IN_SET('{$name}', t.players)");
        }

        if (!is_null($value = Arr::get($attributes, 'game'))) {
            $value = str_replace("　", ' ', $value);
            $query = $query->where('t.game', 'like', "%{$value}%");
        }

        if (!is_null($value = Arr::get($attributes, 'created_start'))) {
            $query = $query->where('m.created_at', '>=', Carbon::parse($value)->format("Y-m-d 00:00:00"));
        }
        if (!is_null($value = Arr::get($attributes, 'created_end'))) {
            $query = $query->where('m.created_at', '<=', Carbon::parse($value)->format("Y-m-d 23:59:59"));
        }
        if (!is_null($value = Arr::get($attributes, 'game_start'))) {
            $query = $query->where('t.game_date', '>=', Carbon::parse($value)->format("Y-m-d"));
        }
        if (!is_null($value = Arr::get($attributes, 'game_end'))) {
            $query = $query->where('t.game_date', '<=', Carbon::parse($value)->format("Y-m-d"));
        }

        if (!is_null($value = Arr::get($attributes, 'has_blank'))) {
            if (intval($value) === 1) {
                $query = $query->whereIn('m.id', function ($subquery) {
                    $subquery->select('media_id')
                        ->from(with(new \App\Models\MediaAWSMeta)->getTable())
                        ->where('game', '=', '')
                        ->orWhereNull('game')
                        ->orWhere('players', '=', '')
                        ->orWhereNull('players')
                        ->orWhere('game_date', '=', '')
                        ->orWhereNull('game_date')
                        ->orWhere('home_team', '=', '')
                        ->orWhereNull('home_team')
                        ->orWhere('away_team', '=', '')
                        ->orWhereNull('away_team');
                });
            } else if (intval($value) === 2) { // 選手名
                $query = $query->whereIn('m.id', function ($subquery) {
                    $subquery->select('media_id')
                        ->from(with(new \App\Models\MediaAWSMeta)->getTable())
                        ->where('players', '=', '')
                        ->orWhereNull('players');
                });
            } else if (intval($value) === 3) { // クラブ名
                $query = $query->whereNull('club_id');
            }
        }

        $query->select([
            'm.id', 'm.creator', 't.event', 't.game', 't.game_place', 't.game_date', 't.game_time', 't.home_team', 't.away_team', 't.players', 't.subject1', 't.subject2', 't.subject3', 't.state1', 't.state2', 't.state3', 't.group_name', 'm.is_done', 'm.is_top', 'm.filename', 'm.extension','m.folder', 'm.club_id', 'm.source', 'a.name', 'm.updated_at', 'm.uploaded_at', 'm.is_show'
        ]);

        $hasSortOption = false;

        if (!is_null($value = Arr::get($attributes, 'sort_option1'))) {
            $option = Arr::get($attributes, 'sort_option_value1');
            if (is_null($option)) {
                $option = 'asc';
            }
            $query = $query->orderBy($value, $option);

            $hasSortOption = true;
        }

        if (!is_null($value = Arr::get($attributes, 'sort_option2'))) {
            $option = Arr::get($attributes, 'sort_option_value2');
            if (is_null($option)) {
                $option = 'asc';
            }
            $query = $query->orderBy($value, $option);

            $hasSortOption = true;
        }

        if (!is_null($value = Arr::get($attributes, 'sort_option3'))) {
            $option = Arr::get($attributes, 'sort_option_value3');
            if (is_null($option)) {
                $option = 'asc';
            }
            $query = $query->orderBy($value, $option);

            $hasSortOption = true;
        }

        if ($hasSortOption === false) {
            $query = $query->orderByDesc('m.is_top')->orderBy('uploaded_at', 'desc');
        }
        //logger()->info($query->toSql());

        return $query;
    }

    /**
     * クラブ名を取得する
     * @param null|int $clubId
     * @return null|Admin[]
     */
    public function getClubs($clubId = null)
    {
        $query = Admin::where('role', 'club')->with('players');

        if (!is_null($clubId)) {
            $query->where('id', $clubId);
        }

        return $query->get();
    }

    public function getSortOptionString($attributes)
    {
        $sortOptions = "";

        if (!is_null($value = Arr::get($attributes, 'sort_option1'))) {
            $option = Arr::get($attributes, 'sort_option_value1');
            if (is_null($option)) {
                $option = 'asc';
            }

            if ($sortOptions !== "") {
                $sortOptions .= "、";
            }
            $sortOptions .= \App\Models\Media::getSortOptions()[$value] . '(' . \App\Models\Media::getSortValueOptions()[$option] . ')';
        }

        if (!is_null($value = Arr::get($attributes, 'sort_option2'))) {
            $option = Arr::get($attributes, 'sort_option_value2');
            if (is_null($option)) {
                $option = 'asc';
            }

            if ($sortOptions !== "") {
                $sortOptions .= "、";
            }
            $sortOptions .= \App\Models\Media::getSortOptions()[$value] . '(' . \App\Models\Media::getSortValueOptions()[$option] . ')';
        }

        if (!is_null($value = Arr::get($attributes, 'sort_option3'))) {
            $option = Arr::get($attributes, 'sort_option_value3');
            if (is_null($option)) {
                $option = 'asc';
            }

            if ($sortOptions !== "") {
                $sortOptions .= "、";
            }
            $sortOptions .= \App\Models\Media::getSortOptions()[$value] . '(' . \App\Models\Media::getSortValueOptions()[$option] . ')';
        }

        if ($sortOptions !== '') {
            $sortOptions = ": " . $sortOptions;
        }

        return $sortOptions;
    }
}
