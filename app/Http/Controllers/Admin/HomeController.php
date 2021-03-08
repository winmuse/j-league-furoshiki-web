<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\SNS\InstagramScrapingService;

class HomeController extends Controller
{
    private $instagramService;

    public function __construct()
    {
        $this->middleware('admin.auth:admin');
    }

    public function index()
    {
        return view('admin.dashboard');
    }
}
