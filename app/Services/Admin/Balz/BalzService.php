<?php

namespace App\Services\Admin\Balz;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

/**
 * Class BalzService
 * @package App\Http\Services\Admin\Balz
 */
class BalzService
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
        $query = Admin::where('role', Admin::BALZ_ROLE);
        $query = $this->scopeLikeBuilder($query, $attributes, 'name');
        $query = $this->scopeLikeBuilder($query, $attributes, 'email');
        $query = $this->scopeLikeBuilder($query, $attributes, 'role');

        $query->orderByDesc('created_at');

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
     * クラブチーム情報を取得する
     *
     * @param int $id
     *
     * @return Admin
     */
    public function getAccount(int $id): Admin
    {
        return Admin::find($id);
    }

    /**
     * クラブチームの更新
     *
     * @param Admin $user
     * @param array $attributes
     */
    public function updateAccount(Admin $user, array $attributes): void
    {
        $userAttributes = Arr::get($attributes, 'admins');

        $password = $userAttributes['password'];
        if(is_null($password)) {
            unset($userAttributes['password']);
        } else {
            $userAttributes['password'] = bcrypt($password);
        }
        $user->update($userAttributes);
    }

    /**
     * クラブチームの登録
     *
     * @param Admin $user
     * @param array $attributes
     */
    public function createAccount(array $attributes): void
    {
        $userAttributes = Arr::get($attributes, 'admins');

        $user = Admin::create([
                    'name' => $userAttributes['name'],
                    'email' => $userAttributes['email'],
                    'password' => Hash::make($userAttributes['password']),
                    'role' => $userAttributes['role'],
                    'name_short' => '',
                    'name_en' => ''
                ]);
    }

    /**
     * Delete
     *
     * @param int $id
     */
    public function deleteAccount(int $id): void
    {
        Admin::find($id)->delete();
    }
}
