<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePut
 * 
 * @package App\Http\Request\Tag
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
            'tags' => ['required', 'array'],
            'tags.name' => ['required', 'string', 'max:255']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'tags.name' => 'ハッシュタグ'
        ];
    }
}