<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\Tel;

/**
 * Class InsertPut
 * 
 * @package App\Http\Request\Account
 */
class InsertPut extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     * @return array
     */
    public function rules()
    {
        return [
            'users' => ['required', 'array'],
            'users.email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'users.name' => ['required', 'string', 'max:255'],
            'users.status' => ['required', 'integer', 'max:2'],
            'users.password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_profiles' => ['required', 'array'],
            'account_profiles.mobile' => ['required', 'string', 'max:20', new Tel()],
            'account_profiles.admin_id' => ['required', 'integer']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'users.name' => '投稿者名',
            'users.status' => 'ステータス',
            'account_profiles.mobile' => '電話番号',
            'users.email' => 'メールアドレス',
            'users.password' => 'パスワード',
            'account_profiles.admin_id' => 'クラブ'
        ];
    }
}