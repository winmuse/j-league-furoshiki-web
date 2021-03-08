<?php

namespace App\Http\Controllers\Admin\Media;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Media\MediaUsageService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MediaController extends Controller
{
    /**
     * @var MediaUsageService
     */
    private $usageService;

    /**
     * MediaController constructor
     * 
     * @param MediaUsageService $usageService
     */
    public function __construct(
        MediaUsageService $usageService
    )
    {
        $this->usageService = $usageService;

        $this->middleware('admin.auth:admin');
    }

    /**
     * 
     * @param Request $request
     * 
     * @return View
     */
    public function index(Request $request): View
    {
        $medias = $this->usageService->search($request->all());
        $users = $this->usageService->getUsers();
        $clubs = $this->usageService->getClubs();

        return view('admin.medias.usages.index', compact('medias', 'users', 'clubs'));
    }

    /**
     * @param int $id
     * 
     * @return View
     */
    public function detail(int $id): View
    {
        $media = $this->usageService->getMediaUsage($id);
        
        return view('admin.medias.usages.detail', compact('media'));
    }

    /**
     * ダウンロード
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $exports = new \App\Exports\MediaUsagesExport();
        $exports->mediaUsages = $this->usageService->searchForExport($request->all());

        return Excel::download($exports, "media_usages.xlsx");
    }
}
