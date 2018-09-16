<?php

namespace App\Http\Controllers\Admins\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Goods\ModelRequest;
use App\Services\Admins\ModelService;
use App\Utils\Page;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index()
    {
        $curPage  = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));

        $page = ModelService::findByPage($curPage, $pageSize);

        return view('admins.goods.models')->with('page', $page);
    }

    public function create()
    {
        return view('admins.goods.editModel');
    }

    public function store(ModelRequest $request)
    {
        $results        = $this->saveOrUpdate();
        $results['url'] = '/admin/model';
        return response()->json($results);
    }

    public function edit($id)
    {
        $model = ModelService::findById($id);
        if (!$model) {
            abort(400, '模型不存在。');
        }

        return view('admins.goods.editModel')->with('data', $model);
    }

    public function update(ModelRequest $request, $id)
    {
        $results        = $this->saveOrUpdate();
        $results['url'] = '/admin/model';
        return response()->json($results);
    }

    public function destroy($id)
    {
        ModelService::destroy(array($id));
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/model',
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

        ModelService::destroy($ids);
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/model',
        ));
    }

    public function findSpecById($id)
    {
        $model = ModelService::findById($id);
        if (!$model) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('模型不存在。'),
                'url'      => '',
            ));
        }

        $attributeList = array();
        foreach ($model->attributes as $attribute) {
            $attr           = $attribute->toArray();
            $values         = explode(',', $attribute->value);
            $attr['values'] = $values;

            $replaceValues = array();
            foreach ($values as $value) {
                $replaceValues[] = replaceSpecialChar($value);
            }
            $attr['replaceValues'] = $replaceValues;
            $attributeList[]       = $attr;
        }

        return response()->json(array(
            'code'     => 200,
            'messages' => array('查询成功。'),
            'url'      => '',
            'datas'    => $attributeList,
        ));
    }

    private function saveOrUpdate()
    {
        $id              = intVal(request('id', 0));
        $name            = trimSpace(request('name', ''));
        $attributeParams = request('attribute', array());

        $result = ModelService::existColumn('name', $name, $id);
        if ($result) {
            return array(
                'code'     => 500,
                'messages' => array('模型名称已存在。'),
                'url'      => '',
            );
        }

        return ModelService::saveOrUpdate($id, $name, $attributeParams);
    }
}
