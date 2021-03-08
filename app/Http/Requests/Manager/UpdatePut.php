<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\Tel;

/**
 * Class UpdatePut
 * 
 * @package App\Http\Request\Manager
 */
class UpdatePut extends FormRequest
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
            'admins.name' => ['required', 'string', 'max:255'],
            'admins.role' => ['required', 'string', 'max:255'],
            'admins.email' => ['nullable', 'string', 'email', 'max:255', 'unique:admins'],
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'admins.name' => '投稿者名',
            'admins.status' => '区分',
            'admins.email' => 'メールアドレス',
        ];
    }
}