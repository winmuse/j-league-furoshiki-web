<?php

namespace App\Http\Requests\Line;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

/**
 * Class IndexGet
 *
 * @package App\Http\Request\Tag
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
    ];
  }

  /**
   * @return array
   */
  public function attributes()
  {
    return [
      'auth_url' => '認証用URL',
      'name' => '名前',
      'access_token' => 'トークン',
      'channel_secret' => 'シークレットキー',
      'valid_flag' => '有効フラグ',
    ];
  }
}
