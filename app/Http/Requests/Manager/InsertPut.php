<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InsertPut
 * 
 * @package App\Http\Request\Manager
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
            'admins' => ['required', 'array'],
            'admins.email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'admins.name' => ['required', 'string', 'max:255'],
            'admins.role' => ['required', 'string', 'max:255'],
            'admins.password' => ['required', 'string', 'min:8', 'confirmed']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'admins.name' => '投稿者名',
            'admins.role' => '区分',
            'admins.email' => 'メールアドレス',
            'admins.password' => 'パスワード'
        ];
    }
}