<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ExpireUpdatePut
 * 
 * @package App\Http\Request\Tag
 */
class ExpireUpdatePut extends FormRequest
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
            'tags' => ['required', 'array'],
            'tags.name' => ['required', 'string', 'max:255'],
            'tags.expire_at' => ['required', 'date_format:Y-m-d'],
            'tags.use_start' => ['required', 'date_format:Y-m-d']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'tags.name' => 'ハッシュタグ',
            'tags.expire_at' => '期間限定日',
            'tags.use_start' => '期間開始日'
        ];
    }
}