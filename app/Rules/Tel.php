<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


/**
 * 電話番号のバリデーション
 * Class Tel
 * @package App\Rules
 */
class Tel implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
//        return (bool) preg_match("/^0\d{2,3}-\d{1,4}-\d{3,4}$/", $value);
        return (bool) preg_match("/^0\d{9}|0\d{2}-\d{3}-\d{4}|0\d{1}-\d{4}-\d{4}|0\d{2}-\d{4}-\d{4}$/", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeは電話番号の形式（例：090-1234-5678）で入力してください。';
    }
}
