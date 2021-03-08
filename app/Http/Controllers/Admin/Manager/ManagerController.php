<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Manager\ManagerService;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use App\Http\Requests\Manager\IndexGet;
use App\Http\Requests\Manager\UpdatePut;
use App\Http\Requests\Manager\InsertPut;
use App\Http\Requests\Manager\InsertPutParent;
use App\Http\Requests\Manager\UpdatePutParent;
use Symfony\Component\HttpFoundation\RedirectResponse;
//use Illuminate\Support\Arr;

class ManagerController extends Controller
{
    /**
     * @var ManagerService
     */
    private $service;

    /**
     * ManagerController constructor
     * 
     * @param ManagerService $managerService
     */
    public function __construct(
        ManagerService $managerService
    )
    {
        $this->service = $managerService;

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
        $managers = $this->service->search($request->validated());

        $request->session()->put('manager_search', $request->all());

        return view('admin.managers.index', compact('managers'));
    }

    /**
     * Create Form
     * 
     * @return View
     */
    public function create(): View
    {
        $managers = $this->service->getAllManagers();

        return view('admin.managers.create', compact('managers'));
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
        $manager = $this->service->getAccount($id);
        $managers = $this->service->getAllManagers();

        return view('admin.managers.edit', compact('manager', 'managers'));
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
        /** @var User $user */
        $user = $this->service->getAccount(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateAccount($user, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'クラブチーム情報を変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('manager_search'))) {
            $params = $request->session()->get('manager_search');
        }
        
        return redirect()->route('admin.managers.index', $params)
                ->with(['system.message.success' => 'クラブチームの情報を保存しました。']);
    }

    /**
     * Update
     * 
     * @param UpdatePutParent $request
     * 
     * @return RedirectResponse
     */
    public function updateWithParent(UpdatePutParent $request): RedirectResponse
    {
        /** @var User $user */
        $user = $this->service->getAccount(intval(request('id')));

        \DB::beginTransaction();

        try {
            $this->service->updateAccount($user, $request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.edit', ['id' => $user->id])
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'クラブチーム情報を変更できません']);
        }

        \DB::commit();

        $params = [];
        if (!is_null($request->session()->get('manager_search'))) {
            $params = $request->session()->get('manager_search');
        }
        
        return redirect()->route('admin.managers.index', $params)
                ->with(['system.message.success' => 'クラブチームの情報を保存しました。']);
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
        /** @var User $user */
        \DB::beginTransaction();

        try {
            $this->service->createAccount($request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'クラブチーム情報を登録できません']);
        }

        \DB::commit();
        
        return redirect()->route('admin.managers.index')
                ->with(['system.message.success' => 'クラブチームを登録しました。']);
    }

    /**
     * Store
     * 
     * @param InsertPutParent $request
     * 
     * @return RedirectResponse
     * 
     */
    public function storeWithParent(InsertPutParent $request): RedirectResponse
    {
        /** @var User $user */
        \DB::beginTransaction();

        try {
            $this->service->createAccountWithParent($request->all());
        } catch (\LogicException $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => $e->getMessage()]);
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            \DB::rollBack();
            return redirect()->route('admin.managers.create')
                    ->withInput($request->all())
                    ->with(['system.message.danger' => 'クラブチーム情報を登録できません']);
        }

        \DB::commit();
        
        return redirect()->route('admin.managers.index')
                ->with(['system.message.success' => 'クラブチームを登録しました。']);
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
        if (!is_null($request->session()->get('manager_search'))) {
            $params = $request->session()->get('manager_search');
        }

        return redirect()->route('admin.managers.index', $params)
                ->with(['system.message.success' => 'クラブチームは削除しました。']);
    }
}
