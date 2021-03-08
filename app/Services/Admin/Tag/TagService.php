<?php

namespace App\Services\Admin\Tag;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Master\Tag;
use Illuminate\Support\Facades\Hash;

/**
 * Class TagService
 * @package App\Http\Services\Admin\Tag
 */
class TagService
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
        $query = Tag::whereNotNull('id')->with('club');
        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $query->where('type', \App\Models\Admin::CLUB_ROLE)
                  ->where('club_id', $user->id);
        }
        $query = $this->scopeLikeBuilder($query, $attributes, 'name');

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

        return $query->where($column, 'like', "%{$value}%");
    }

    /**
     * ハッシュタグを取得する
     * 
     * @param int $id
     * 
     * @return Tag
     */
    public function getTag(int $id): Tag
    {
        return Tag::find($id);
    }

    /**
     * ハッシュタグの更新
     * 
     * @param Tag $tag
     * @param array $attributes
     */
    public function updateTag(Tag $tag, array $attributes): void
    {
        $attributes = Arr::get($attributes, 'tags');

        $tag->update($attributes);
    }

    /**
     * ハッシュタグの登録
     * 
     * @param Tag $tag
     * @param array $attributes
     */
    public function createTag(array $attributes): void
    {
        $attributes = Arr::get($attributes, 'tags');

        $data = [
            'name' => $attributes['name'],
            'type' => auth()->user()->role
        ];

        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $data['club_id'] = $user->id;
        }

        Tag::create($data);
    }

    /**
     * Delete
     * 
     * @param int $id
     */
    public function deleteTag(int $id): void
    {
        Tag::find($id)->delete();
    }
}