<?php

namespace App\Services\Admin\Manager;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

/**
 * Class ManagerService
 * @package App\Http\Services\Admin\Manager
 */
class ManagerService
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
        // $query = Admin::where('role', '!=', 'admin');
        $query = Admin::where('role', Admin::CLUB_ROLE);
        $query = $this->scopeLikeBuilder($query, $attributes, 'name');
        $query = $this->scopeLikeBuilder($query, $attributes, 'email');
        $query = $this->scopeLikeBuilder($query, $attributes, 'role');

        $query->orderByDesc('created_at');

        return $query->paginate(static::PER_PAGE);
    }

    public function getAllManagers()
    {
        $query = Admin::where('role', Admin::CLUB_ROLE)
                    ->whereNull('parent_admin_id')
                    ->select(['id', 'name']);

        return $query->get();
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

        if (is_null($userAttributes['email'])) {
            unset($userAttributes['email']);
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
                    'name_short' => $userAttributes['name_short'],
                    'name_en' => $userAttributes['name_en'],
                    'email' => $userAttributes['email'],
                    'password' => Hash::make($userAttributes['password']),
                    'role' => $userAttributes['role']
                ]);

        $user->dropbox()->updateOrCreate([
            'admin_id' => $user->id
        ], [
            'app_key' => '',
            'app_secret' => '',
            '_token' => '',
            'folder' => ''
        ]);
    }

    /**
     * クラブチームの登録
     *
     * @param Admin $user
     * @param array $attributes
     */
    public function createAccountWithParent(array $attributes): void
    {
        $userAttributes = Arr::get($attributes, 'admins');

        $user = Admin::create([
                    'email' => $userAttributes['email'],
                    'password' => Hash::make($userAttributes['password']),
                    'role' => $userAttributes['role'],
                    'parent_admin_id' => $userAttributes['parent_admin_id'],
                    'name' => '',
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
