<?php

namespace App\Services\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Media;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class MediaUsageService
 * @package App\Http\Services\Media
 */
class MediaUsageService
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
        return $this->getQuery($attributes)->paginate(static::PER_PAGE);
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

        return $query->where($column, 'like', "%{$value}%");
    }

    /**
     * @return App\Models\User[] $users
     */
    public function getUsers()
    {
        $users = \App\Models\User::where('status', 1)
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                    ->orderBy('name')
                    ->select(['users.id', 'users.name', 'profiles.admin_id']);

        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $adminID = $user->id;
            $users->whereIn('users.id', function ($query) use ($adminID) {
                $query->select('user_id')
                    ->from(with(new \App\Models\Profile)->getTable())
                    ->where('admin_id', $adminID);
            });
        }

        return $users->get();
    }

    /**
     * @return App\Models\Admin[] $clubs
     */
    public function getClubs()
    {
        $clubs = \App\Models\Admin::where('role', \App\Models\Admin::CLUB_ROLE)->select(['id', 'name'])->get();

        return $clubs;
    }

    /**
     * @return App\Models\Media
     */
    public function getMediaUsage(int $id)
    {
        return Media::where('id', $id)->with('meta')->with('articles')->first();
    }

    /**
     * @param array $attributes
     *
     */
    public function searchForExport(array $attributes)
    {
        return $this->getQuery($attributes)->get();
    }

    public function getQuery(array $attributes)
    {
        $query = Media::query();

        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $query = $query->whereHas('clubArticles')->with('clubArticles')->with('meta')->withCount('clubArticles')->orderByDesc('club_articles_count');

            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $adminID = $user->id;
            $query->whereIn('id', function ($query1) use ($adminID) {
                $query1->select('media_id')
                    ->from(with(new \App\Models\ArticleMedia)->getTable())
                    ->whereIn('article_id', function ($query2) use ($adminID) {
                        $query2->select('id')
                            ->from(with(new \App\Models\Article)->getTable())
                            ->whereIn('user_id', function ($query3) use ($adminID) {
                                $query3->select('user_id')
                                    ->from(with(new \App\Models\Profile)->getTable())
                                    ->where('admin_id', $adminID);
                            });
                    });
            });
        } else {
            $query = $query->whereHas('articles')->with('articles')->with('meta')->withCount('articles')->orderByDesc('articles_count');
        }
        
        // クラブ名
        if (!empty($value = Arr::get($attributes, 'club')) && intval($value) > 0) {
            $query->whereIn('id', function ($articleMediaQuery) use ($value) {
                $articleMediaQuery->select('media_id')
                    ->from(with(new \App\Models\ArticleMedia)->getTable())
                    ->whereIn('article_id', function ($articleQuery) use ($value) {
                        $articleQuery->select('articles.id')
                            ->from(with(new \App\Models\Article)->getTable())
                            ->leftJoin('users', 'articles.user_id', '=', 'users.id')
                            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                            ->where('profiles.admin_id', $value);
                    });
            });
        }
        
        // 選手名
        if (!empty($value = Arr::get($attributes, 'players')) && intval($value) > 0) {
            $query->whereIn('id', function ($articleMediaQuery) use ($value) {
                $articleMediaQuery->select('media_id')
                    ->from(with(new \App\Models\ArticleMedia)->getTable())
                    ->whereIn('article_id', function ($articleQuery) use ($value) {
                        $articleQuery->select('id')
                            ->from(with(new \App\Models\Article)->getTable())
                            ->where('user_id', $value);
                    });
            });
        }

        // 上位表示メタデータ
        if (!is_null($value = Arr::get($attributes, 'is_top'))) {
            $query->where('is_top', $value);
        }

        // Regarding Articles
        $query->whereIn('id', function ($articleMediaQuery) use ($attributes) {
            $articleMediaQuery->select('media_id')
                ->from(with(new \App\Models\ArticleMedia)->getTable())
                ->whereIn('article_id', function ($articleQuery) use ($attributes) {
                    $articleQuery->select('id')
                        ->from(with(new \App\Models\Article)->getTable())
                        ->whereNotNull('id');

                    // 投稿日（から）
                    if (!empty($value = Arr::get($attributes, 'created_start'))) {
                        $articleQuery->where('publish_at', '>=', "{$value} 00:00:00");
                    }

                    // 投稿日（まで）
                    if (!empty($value = Arr::get($attributes, 'created_end'))) {
                        $articleQuery->where('publish_at', '<=', "{$value} 23:59:59");
                    }
                    
                    // 投稿内容
                    if (!empty($value = Arr::get($attributes, 'content'))) {
                        $articleQuery->where('description', 'like', "%{$value}%");
                    }

                    // ハッシュタグ
                    if (!empty($value = Arr::get($attributes, 'tag'))) {
                        $articleQuery->whereIn('id', function($tagQuery) use ($value) {
                            $tagQuery->select('article_id')
                                ->from(with(new \App\Models\Tag)->getTable())
                                ->where('name', 'like', "%{$value}%");
                        });
                    }
                });
        });

        // \DB::enableQueryLog(); 

        // $query->get();

        // dd(\DB::getQueryLog()); 

        return $query;
    }
}
