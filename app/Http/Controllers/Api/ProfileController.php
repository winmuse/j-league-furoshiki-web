<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
  public function __construct()
  {
  }

  /**
   * update password
   * @param Request $request (password, passowrd_confirm)
   * @return JsonResponse
   */
  public function updatePassword(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
//      'email' => 'required|string|max:255|email',
      'password' => 'required|string|min:8'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors()->toJson(), 400);
    }
    $userId = Auth::id();
    $user = User::find($userId);
    $user->password = bcrypt($request->input('password'));
    $user->save();
    return response()->json([
      'error' => ''
    ], 200);
  }

  public function updatePhoneNumber(Request $request)
  {
    $validator = Validator::make($request->all(), [
//      'email' => 'required|string|max:255|email',
      'mobile' => 'required|string'
    ]);
    if ($validator->fails()) {
      return response()->json($validator->errors()->toJson(), 400);
    }
    $userId = Auth::id();

    $profile = User::find($userId)->profile;
    $profile->mobile = $request->input('mobile');
    $profile->save();
    return response()->json([
      'error' => ''
    ], 200);
  }
}
