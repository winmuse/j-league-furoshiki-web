<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePut
 *
 * @package App\Http\Request\Comment
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
            'comments' => ['required', 'array'],
            'comments.name' => ['required', 'string', 'max:255']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'comments.name' => 'コメント'
        ];
    }
}
