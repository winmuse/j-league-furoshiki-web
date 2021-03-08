<?php

namespace App\Services\Admin\Account;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class AccountService
 * @package App\Http\Services\Admin\Account
 */
class AccountService
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
        $query = User::whereNotNull('id')->with('profile');
        $admin_id = null;
        if (auth()->user()->role === \App\Models\Admin::CLUB_ROLE) {
            $user = is_null(auth()->user()->parent) ? auth()->user() : auth()->user()->parent;
            $admin_id = $user->id;
        } else {
            if (! empty($admin_id = Arr::get($attributes, 'club'))) {
                $admin_id = intval($admin_id) > 0 ? intval($admin_id) : null;
            }
        }

        if ($admin_id) {
            $query = User::whereIn('id', function($query) use ($admin_id) {
                $query->select('user_id')
                    ->from(with(new \App\Models\Profile)->getTable())
                    ->where('admin_id', $admin_id);
            });
        }

        $query = $this->scopeNumberBuilder($query, $attributes, 'player_no');
        $query = $this->scopeLikeBuilder($query, $attributes, 'name');
        $query = $this->scopeLikeBuilder($query, $attributes, 'email');
        $query = $this->scopeNumberBuilder($query, $attributes, 'status');

        $query->orderByDesc('id');

        return $query->paginate(static::PER_PAGE);
    }

    /**
     * 
     * @return App\Models\Admin[] $clubs
     */
    public function getClubs()
    {
        return \App\Models\Admin::where('role', \App\Models\Admin::CLUB_ROLE)->select(['id', 'name'])->get();
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
     * @param Builder $query
     * @param array   $attributes
     * @param string  $column
     * 
     * @return Builder
     */
    private function scopeNumberBuilder(Builder $query, array $attributes, string $column): Builder
    {
        $value = Arr::get($attributes, $column);
        if ($value !== '0' && empty($value)) {
            return $query;
        }

        return $query->where($column, $value);
    }

    /**
     * アカウント情報を取得する
     * 
     * @param int $id
     * 
     * @return User
     */
    public function getAccount(int $id): User
    {
        return User::with('profile')->find($id);
    }

    /**
     * アカウントの更新
     * 
     * @param User $user
     * @param array $attributes
     */
    public function updateAccount(User $user, array $attributes): void
    {
        $userAttributes = Arr::get($attributes, 'users');
        $profileAttributes = Arr::get($attributes, 'account_profiles');

        if (isset($userAttributes['password'])) {
            $userAttributes['password'] = Hash::make($userAttributes['password']);
            unset($userAttributes['password_confirmation']);
        } else {
            unset($userAttributes['password_confirmation']);
            unset($userAttributes['password']);
        }

        if (is_null($userAttributes['email']) || $userAttributes['email'] === '') {
            unset($userAttributes['email']);
        }
        if (is_null($profileAttributes['mobile']) || $profileAttributes['mobile'] === '') {
            unset($profileAttributes['mobile']);
        }

        $user->update($userAttributes);
        $user->profile()->updateOrCreate([
            'user_id' => $user->id
        ], $profileAttributes);
    }

    /**
     * アカウントの登録
     * 
     * @param User $user
     * @param array $attributes
     */
    public function createAccount(array $attributes): void
    {
        $userAttributes = Arr::get($attributes, 'users');
        $profileAttributes = Arr::get($attributes, 'account_profiles');

        $user = User::create([
                    'name' => $userAttributes['name'],
                    'email' => $userAttributes['email'],
                    'password' => Hash::make($userAttributes['password']),
                    'status' => $userAttributes['status']
                ]);

        $user->profile()->updateOrCreate([
            'user_id' => $user->id
        ], $profileAttributes);
    }

    /**
     * Delete
     * 
     * @param int $id
     */
    public function deleteAccount(int $id): void
    {
        User::find($id)->delete();
    }
}