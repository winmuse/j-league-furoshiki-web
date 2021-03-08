<?php

namespace App\Http\Controllers\Api\SNS;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\SNS\LineService;
use Illuminate\Support\Facades\Auth;

class LineController extends BaseController
{
  private $api;

  public function __construct(LineService $lineService)
  {
    $this->middleware(function ($request, $next) use ($lineService) {
      $this->api = $lineService;
      return $next($request);
    });
  }

    /**
     * get user's SNS credential array
     * @return JsonResponse
     */
    public function get():JsonResponse
    {
        $userId = Auth::id();
        $line_credential = User::find($userId)->line_credential;
        return response()->json(['line_credential' => $line_credential]);
    }

  public function save(Request $request)
  {
    $user = User::find(Auth::id());

    if(!$user) {
      return response()->json(['error' => 'not authenticated']);
    }

    $success = $this->api->updateOrCreate($request, $user);
    if ($success)
      return response()->json(['error' => '']);
    else
      return response()->json(['error' => 'DB error'], 400);
  }
}
