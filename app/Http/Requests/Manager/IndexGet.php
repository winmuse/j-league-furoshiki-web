<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

/**
 * Class IndexGet
 * 
 * @package App\Http\Request\Manager
 */
class IndexGet extends FormRequest
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
            'name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                'nullable',
                'string',
                'max:255'
            ],
            'role' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '投稿者名',
            'email' => 'メールアドレス',
            'role' => '区分'
        ];
    }
}