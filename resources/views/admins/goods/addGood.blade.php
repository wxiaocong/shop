@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/1">商品</a>
        </li>
        <li>
            <a href="/admin/good">商品管理</a>
        </li>
        <li class="active">商品添加</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <form class="edit-good-form" action="{{ url('/admin/good') }}">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">商品信息</a></li>
            <li><a href="#tab2" data-toggle="tab">描述</a></li>
            <li><a href="#tab3" data-toggle="tab">营销选项</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <table class="table form-table">
                    <colgroup>
                        <col width="130px" />
                        <col />
                    </colgroup>
                    <tr>
                        <th>商品名称：</th>
                        <td>
                            <input class="form-control" name="name" type="text" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <th>虚实类型：</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="virtualType" value="1" checked>实体商品
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="virtualType" value="0">虚拟商品
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>是否上架：</th>
                        <td class="good-state">
                            <label class="radio-inline">
                                <input type="radio" name="state" value="0" checked>是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="state" value="2">否
                            </label>
                            <p class="help-block">只有上架的商品才会在前台显示出来，客户是无法看到下架商品</p>
                        </td>
                    </tr>
                    <tr>
                        <th>商品推荐：</th>
                        <td>
                            <label class="checkbox-inline"><input name="hot" type="checkbox" value="1">热卖</label>
                            <label class="checkbox-inline"><input name="new" type="checkbox" value="1">新品 </label>
                            <label class="checkbox-inline"><input name="best" type="checkbox" value="1">精品 </label>
                            <label class="checkbox-inline"><input name="recommend" type="checkbox" value="1">推荐 </label>
                            <label class="checkbox-inline"><input name="freeshipping" type="checkbox" value="1">包邮 </label>
                        </td>
                    </tr>
                    <tr>
                        <th>排序：</th>
                        <td>
                            <input class="form-control" type="text" name="sort" value="99" onkeyup="onlyNum(this)"/>
                        </td>
                    </tr>
                    <tr>
                        <th>所属商户：</th>
                        <td>
                            <select class="form-control" name="merchantId">
                                <option value="0">52gai自营 </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>所属品牌：</th>
                        <td>
                            <select class="form-control" name="brandId">
                                <option value="0">选择品牌</option>
                                @foreach ($brandList as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->short_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>所属分类：</th>
                        <td>
                            <div class="category-box" style="float:left;">
                            </div>
                            <button class="btn btn-primary goods-category-button" type="button"><i class="fa fa-list"></i> 选择分类</button>
                        </td>
                    </tr>
                    <tr>
                        <th>商品模型：</th>
                        <td>
                            <select class="form-control model-select" name="modelTypeId">
                                <option value="0">通用类型 </option>
                                @foreach ($modelList as $model)
                                    <option value="{{ $model->id }}">{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>商品属性：</th>
                        <td>
                            <table class="table table-bordered">
                                <tbody class="good-attribute-tbody">
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th>商品sku：</th>
                        <td>
                            <p class="help-block">*sku库存或销售价格为0,sku默认下架</p>
                            <table class="table table-bordered">
                                <thead class="good-sku-thead">
                                    <tr>
                                        <td style="width: 10%">sku名称</td>
                                        <td style="width: 9%">sku编码（料号）</td>
                                        <td style="width: 9%">sku条码</td>
                                        <td class="good-sku-thead-number" style="width: 5%">库存</td>
                                        <td style="width: 5%">预警库存</td>
                                        <td style="width: 5%">销售价格</td>
                                        <td style="width: 5%">会员价格</td>
                                        <td style="width: 5%">批发价格</td>
                                        <td style="width: 5%">成本价格</td>
                                        <td style="width: 5%">重量(克)</td>
                                        <td style="width: 6%">状态</td>
                                        <td style="width: 3%">图片</td>
                                    </tr>
                                </thead>
                                <tbody class="good-sku-tbody">
                                    <tr>
                                        <td><input class="form-control input-sm" name="goodSku[name][]" type="text" value=""/></td>
                                        <td><input class="form-control input-sm" name="goodSku[custNo][]" type="text" value=""/></td>
                                        <td><input class="form-control input-sm" name="goodSku[barCode][]" type="text" value=""/></td>
                                        <td class="good-sku-tbody-number">
                                        <input name="goodSku[specIds][]" type="hidden" value="0"/>
                                        <input name="goodSku[specValues][]" type="hidden" value="0"/>
                                        <input class="form-control input-sm" name="goodSku[storeNum][]" type="text" value="0" onkeyup="onlyNum(this)"/></td>
                                        <td><input class="form-control input-sm" name="goodSku[warningNum][]" type="text" value="0" onkeyup="onlyNum(this)"/></td>
                                        <td><input class="form-control input-sm" name="goodSku[sellPrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>
                                        <td><input class="form-control input-sm" name="goodSku[memberPrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>
                                        <td><input class="form-control input-sm" name="goodSku[wholesalePrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>
                                        <td><input class="form-control input-sm" name="goodSku[costPrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>
                                        <td><input class="form-control input-sm" name="goodSku[weight][]" type="text" value="0" onkeyup="onlyNum(this)"/></td>
                                        <td class="good-sku-state">
                                            <select class="form-control input-sm" name="goodSku[state][]">
                                                <option value="0" selected>上架</option>
                                                <option value="2">下架</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#goodSku0" type="button">图片</button>
                                            <div class="modal fade" id="goodSku0" tabindex="-1" role="dialog" aria-labelledby="goodSku0Label" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h4 class="modal-title" id="goodSku0Label">显示/上传商品Sku图片</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input class="fileUpload file-loading" type="file" name="file" multiple>
                                                            <input type="hidden" value="0"/>
                                                            <p class="help-block">建议高度400px,宽度和高度比例为1：1</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane" id="tab2">
                <table class="table form-table">
                    <colgroup>
                        <col width="130px" />
                        <col />
                    </colgroup>
                    <tr>
                        <th>产品描述：</th>
                        <td><textarea id="container" name="content" style="width:100%;height:400px;"></textarea></td>
                    </tr>
                </table>
            </div>

            <div class="tab-pane" id="tab3">
                <table class="table form-table">
                    <colgroup>
                        <col width="130px" />
                        <col />
                    </colgroup>

                    <tr>
                        <th>SEO关键词：</th><td><input class="form-control" name="keywords" type="text" value="" /><span>用于SEO，多个关键字请用英文逗号分隔</span></td>
                    </tr>
                    <tr>
                        <th>SEO描述：</th><td><textarea class="form-control" name="description"></textarea></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-center">
            <button class='btn btn-primary good-submit' type="submit">发布商品</button>
        </div>
    </div>
    </form>
</div>
@include('include.message')
@include('admins.goods.include.categoryDialog')
<script src="{{ asset('js/common/fileinput/js/plugins/sortable.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/fileinput.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/locales/zh.js') }}"></script>
<script src="{{ asset('js/common/fileinput/themes/explorer/theme.js') }}"></script>
<link href="{{ asset('js/common/ueditor/themes/default/css/ueditor.css') }}" rel="stylesheet">
<script src="{{ asset('js/common/ueditor/ueditor.config.js') }}"></script>
<script src="{{ asset('js/common/ueditor/ueditor.all.js') }}"></script>
<script type="text/javascript">
    var ue = UE.getEditor('container',{
        initialFrameWidth : '100%',//宽度
        initialFrameHeight: 400//高度
    });
</script>
<script src="{{ elixir('js/common/fileUpload.js') }}"></script>
<script src="{{ elixir('js/admins/goodCategory.js') }}"></script>
<script src="{{ elixir('js/admins/good.js') }}"></script>
@include('admins.footer')
