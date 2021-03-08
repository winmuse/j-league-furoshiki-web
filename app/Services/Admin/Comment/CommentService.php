<?php

namespace App\Services\Admin\Comment;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Master\Comment;
use Illuminate\Support\Facades\Hash;

/**
 * Class CommentService
 * @package App\Http\Services\Admin\Comment
 */
class CommentService
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
        $query = Comment::whereNotNull('id')->with('club');
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
     * コメントを取得する
     *
     * @param int $id
     *
     * @return Comment
     */
    public function getComment(int $id): Comment
    {
        return Comment::find($id);
    }

    /**
     * コメントの更新
     *
     * @param Comment $comment
     * @param array $attributes
     */
    public function updateComment(Comment $comment, array $attributes): void
    {
        $attributes = Arr::get($attributes, 'comments');

        $comment->update($attributes);
    }

    /**
     * コメントの登録
     *
     * @param Comment comment
     * @param array $attributes
     */
    public function createComment(array $attributes): void
    {
        $attributes = Arr::get($attributes, 'comments');

        $data = [
            'name' => $attributes['name'],
            'type' => auth()->user()->role
        ];

        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $data['club_id'] = $user->id;
        }

        Comment::create($data);
    }

    /**
     * Delete
     *
     * @param int $id
     */
    public function deleteComment(int $id): void
    {
        Comment::find($id)->delete();
    }
}
