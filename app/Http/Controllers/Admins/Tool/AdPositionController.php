<?php

namespace App\Http\Controllers\Admins\Tool;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Tool\AdPositionRequest;
use App\Services\AdPositionService;
use App\Utils\Page;
use Illuminate\Http\Request;

class AdPositionController extends Controller
{
    public function index()
    {
        $params   = array();
        $curPage  = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));

        $page = AdPositionService::findByPageAndParams($curPage, $pageSize, $params);
        return view('admins.tool.adPositions')
            ->with('page', $page);
    }

    public function create()
    {
        return view('admins.tool.editAdPosition');
    }

    public function store(AdPositionRequest $request)
    {
        $results        = AdPositionService::saveOrUpdate($request);
        $results['url'] = '/admin/ad';
        return response()->json($results);
    }

    public function edit($id)
    {
        $ad = AdPositionService::findById($id);
        if (!$ad) {
            abort(400, '轮播图不存在');
        }

        return view('admins.tool.editAdPosition')->with('data', $ad);
    }

    public function update(AdPositionRequest $request, $id)
    {
        $results        = AdPositionService::saveOrUpdate($request);
        $results['url'] = '/admin/ad';
        return response()->json($results);
    }

    public function destroy($id)
    {
        AdPositionService::destroy(array($id));
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/ad',
        ));
    }

    public function destroyAll()
    {
        $ids = request('ids', array());
        if (count($ids) == 0) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }

        AdPositionService::destroy($ids);
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/ad',
        ));
    }
}
