<?php

namespace App\Http\Requests\Line;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePut
 *
 * @package App\Http\Request\Line
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
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
        ];
    }
}
