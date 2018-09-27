<?php

namespace App\Http\Controllers\Admins\System;

use App\Http\Controllers\Controller;
use App\Services\Admins\SystemService;
use App\Http\Requests\Admins\System\SystemRequest;
use App\Utils\Page;

class SystemController extends Controller {
    public function index() {
        $params = array();
        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search'] = trimSpace(request('search', ''));

        $page = SystemService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.system.systemList')
            ->with('search', $params['search'])
            ->with('page', $page);
    }

    public function create() {
        return view('admins.system.editSystem');
    }

    public function store(SystemRequest $request) {
        $results = SystemService::saveOrUpdate();
        $results['url'] = '/admin/system';
        return response()->json($results);
    }

    public function edit($id) {
        $param = SystemService::findById($id);
        if (!$param) {
            abort(400, '参数不存在');
        }

        return view('admins.system.editSystem')->with('param', $param);
    }

    public function update(SystemRequest $request, $id) {
        $results = SystemService::saveOrUpdate();
        $results['url'] = '/admin/system';
        return response()->json($results);
    }

    public function destroy($id) {
        SystemService::destroy(array($id));
        return response()->json(array(
            'code' => 200,
            'messages' => array('删除成功'),
            'url' => '/admin/system',
        ));
    }
}
