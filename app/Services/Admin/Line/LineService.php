<?php

namespace App\Services\Admin\Line;

use App\Models\LineCredential;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class LineService
 * @package App\Http\Services\Admin\Line
 */
class LineService
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
        $query = LineCredential::whereNotNull('id');

        return $query->paginate(static::PER_PAGE);
    }

    /**
     * LINEを取得する
     *
     * @param int $id
     *
     * @return LineCredential
     */
    public function getLine(int $id): LineCredential
    {
        return LineCredential::find($id);
    }

    /**
     * LINEの更新
     *
     * @param LineCredential $line
     * @param array $attributes
     */
    public function updateLine(LineCredential $line, array $attributes): void
    {
        $attributes = Arr::get($attributes, 'lines');

        $line->update($attributes);
    }
}
