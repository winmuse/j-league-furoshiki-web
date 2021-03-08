<?php

namespace App\Services\Admin\Tag;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Master\ExpireTag;
use Illuminate\Support\Facades\Hash;

/**
 * Class ExpireTagService
 * @package App\Http\Services\Admin\Tag
 */
class ExpireTagService
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
        $query = ExpireTag::whereNotNull('id')->with('club');
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
     * @return ExpireTag
     */
    public function getTag(int $id): ExpireTag 
    {
        return ExpireTag::find($id);
    }

    /**
     * ハッシュタグの更新
     * 
     * @param ExpireTag $tag
     * @param array $attributes
     */
    public function updateTag(ExpireTag $tag, array $attributes): void
    {
        $attributes = Arr::get($attributes, 'tags');

        $tag->update($attributes);
    }

    /**
     * ハッシュタグの登録
     * 
     * @param ExpireTag $tag
     * @param array $attributes
     */
    public function createTag(array $attributes): void
    {
        $attributes = Arr::get($attributes, 'tags');

        $data = [
            'name' => $attributes['name'],
            'type' => auth()->user()->role,
            'expire_at' => $attributes['expire_at'],
            'use_start' => $attributes['use_start']
        ];

        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $data['club_id'] = $user->id;
        }

        ExpireTag::create($data);
    }

    /**
     * Delete
     * 
     * @param int $id
     */
    public function deleteTag(int $id): void
    {
        ExpireTag::find($id)->delete();
    }
}