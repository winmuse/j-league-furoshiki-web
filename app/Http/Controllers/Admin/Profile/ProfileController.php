<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
  public function passwordReset(Request $request) {
//    dd($request->all());
    $validator = Validator::make($request->all(), [
      'password_old' => ['required', 'string', 'max:255'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    if($request->isMethod('GET')) {
      return view('admin.password.reset');
    } else {
      if($validator->fails()) {
        return back()->withErrors($validator);
      }

      $user = Auth::user();
      if (!Hash::check($request->get('password_old'), $user->password)) {
        return back()->with(['system.message.danger' => '古いパスワードが間違っています。']);
      }

      $user->password = bcrypt($request->get('password'));
      $user-> save();

      return back()->with(['system.message.success' => 'パスワードが正常に変更されました。']);
    }
  }
}
