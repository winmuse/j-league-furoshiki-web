<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

/**
 * Class IndexGet
 * 
 * @package App\Http\Request\Account
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
            'player_no' => [
                'nullable',
                'integer'
            ],
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
            'status' => [
                'nullable',
                'integer',
                'max:2'
            ],
            'club' => [
                'nullable',
                'integer'
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'player_no' => '選手番号',
            'name' => '投稿者名',
            'email' => 'メールアドレス',
            'status' => 'ステータス'
        ];
    }
}