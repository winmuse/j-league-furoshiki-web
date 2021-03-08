<?php

namespace App\Http\Controllers\Admin\Balz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Balz\BalzService;
use Illuminate\View\View;
use App\Http\Requests\Balz\IndexGet;
use App\Http\Requests\Balz\UpdatePut;
use App\Http\Requests\Balz\InsertPut;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BalzController extends Controller
{
    /**
     * @var BalzService
     */
    private $service;

    /**
     * BalzController constructor
     * 
     * @param BalzService $service
     */
    public function __construct(
        BalzService $service
    )
    {
        $this->service = $service;

        $this->middleware('admin.auth:admin');
    }

    /**
     * 
     * @param IndexGet $request
     * 
     * @return View
     */
    public function index(IndexGet $request): View
    {
        $balzs = $this->service->search($request->validated());

        $request->session()->put('balz_search', $request->all());

        return view('admin.balzs.index', compact('balzs'));
    }

    /**
     * Create Form
     * 
     * @return View
     */
    public function create(): View
    {
        return view('admin.balzs.create');
    }

    /**
     * 編集ぺーじ
     * 
     * @param int $id
     * 
     * @return View
     */
    public function edit($id): View
    {
        $balz = $this->service->getAccount($id);

        return view('admin.balzs.edit', compact('balz'));
    }

    /**
     * Update
     * 
     * @param UpdatePut $request
     * 
     * @return RedirectResponse
     */
    public function update(UpdatePut $request): RedirectResponse
    {
        /** @var Admin $user */
        $user = $this->service->getAccount(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateAccount($user, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.balzs.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.balzs.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'Balzアカウント情報を変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('balz_search'))) {
            $params = $request->session()->get('balz_search');
        }
        
        return redirect()->route('admin.balzs.index', $params)
                ->with(['system.message.success' => 'Balzアカウントの情報を保存しました。']);
    }

    /**
     * Store
     * 
     * @param InsertPut $request
     * 
     * @return RedirectResponse
     * 
     */
    public function store(InsertPut $request): RedirectResponse
    {
        /** @var Admin $user */
        \DB::beginTransaction();

        try {
            $this->service->createAccount($request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.balzs.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.balzs.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'Balzアカウント情報を登録できません']);
        }

        \DB::commit();
        
        return redirect()->route('admin.balzs.index')
                ->with(['system.message.success' => 'Balzアカウントを登録しました。']);
    }

    /**
     * 削除
     * 
     * @param Request $request
     * 
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $this->service->deleteAccount(request('id'));

        $params = [];
        if (!is_null($request->session()->get('balz_search'))) {
            $params = $request->session()->get('balz_search');
        }

        return redirect()->route('admin.balzs.index', $params)
                ->with(['system.message.success' => 'Balzアカウントは削除しました。']);
    }
}
