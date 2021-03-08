<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\Tel;

/**
 * Class UpdatePutParent
 * 
 * @package App\Http\Request\Manager
 */
class UpdatePutParent extends FormRequest
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
            'admins.parent_admin_id' => ['required', 'integer'],
            'admins.role' => ['required', 'string', 'max:255']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'admins.parent_admin_id' => '連携クラブ'
        ];
    }
}