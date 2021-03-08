<?php

namespace App\Http\Controllers\Admin\Dropbox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Dropbox\DropboxService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DropboxController extends Controller
{
    /**
     * @var DropboxService
     */
    private $service;

    /**
     * DropboxController constructor
     * 
     * @param DropboxService $service
     */
    public function __construct(
        DropboxService $service
    )
    {
        $this->service = $service;

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
        $user = auth()->user();
        if (!is_null($user->parent)) {
            $user = $user->parent;
        }

        $account = $user->dropbox;

        if (empty($account)) {
            auth()->user()->dropbox()->updateOrCreate([
                'admin_id' => $user->id
            ], [
                'app_key' => '',
                'app_secret' => '',
                '_token' => '',
                'folder' => ''
            ]);

            $account = \App\Models\DropboxAccount::where('admin_id', $user->id)->first();
        }

        return view('admin.dropbox.index', compact('account'));
    }

    /**
     * Test Dropbox Token & Folder
     * 
     * @param Request $request
     * 
     * @return RedirectResponse
     */
    public function testDropboxAccount(Request $request): RedirectResponse
    {
        $valid = $this->service->testDropbox($request->all());

        if ($valid) {
            return redirect()->route('admin.dropbox.index')
                ->withInput($request->all())
                ->with(['system.message.success' => 'アクセスできました。']); 
        } else {
            return redirect()->route('admin.dropbox.index')
                ->withInput($request->all())
                ->with(['system.message.danger' => 'アクセスできません。']); 
        }
    }

    /**
     * Update
     * 
     * @param Request $request
     * 
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        \DB::beginTransaction();

        try {
            $user = auth()->user();
            if (!is_null($user->parent)) {
                $user = $user->parent;
            }
            $this->service->updateDropbox($user, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.dropbox.index')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.dropbox.index')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => '情報を変更できません']);
        }

        \DB::commit();
        
        return redirect()->route('admin.dropbox.index')
                ->with(['system.message.success' => '情報を保存しました。']);
    }
}
