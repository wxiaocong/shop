<?php

namespace App\Http\Controllers\Admins\Member;

use App\Http\Controllers\Controller;
use App\Services\AgentTypeService;
use App\Http\Requests\Admins\Member\AgentTypeRequest;
use App\Utils\Page;

class AgentTypeController extends Controller {
    public function index() {
        $params = array();
        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search'] = trimSpace(request('search', ''));

        $page = AgentTypeService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.member.agentTypeList')
            ->with('search', $params['search'])
            ->with('page', $page);
    }

    public function create() {
        return view('admins.member.editAgentType');
    }

    public function store(AgentTypeRequest $request) {
        $results = AgentTypeService::saveOrUpdate();
        $results['url'] = '/admin/agentType';
        return response()->json($results);
    }

    public function edit($id) {
        $param = AgentTypeService::findById($id);
        if (!$param) {
            abort(400, '参数不存在');
        }

        return view('admins.member.editAgentType')->with('param', $param);
    }

    public function update(AgentTypeRequest $request, $id) {
        $results = AgentTypeService::saveOrUpdate();
        $results['url'] = '/admin/agentType';
        return response()->json($results);
    }

    public function destroy($id) {
        AgentTypeService::destroy(array($id));
        return response()->json(array(
            'code' => 200,
            'messages' => array('删除成功'),
            'url' => '/admin/agentType',
        ));
    }
}
