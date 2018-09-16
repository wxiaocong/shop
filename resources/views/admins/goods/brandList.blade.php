@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/1">商品</a>
        </li>
        <li>
            <a href="#">品牌管理</a>
        </li>
        <li class="active">品牌列表</li>
    </ul>
</div>
<div class="content">
    <table class="table list-table">
        <colgroup>
            <col width="80px" />
            <col />
            <col />
            <col />
            <col width="80px" />
            <col width="100px" />
        </colgroup>
        <caption>
            <a class="btn btn-default" href="/admin/goods/brand/create">
                <i class="fa fa-plus"></i> 添加品牌
            </a>
            <form action="{{ url('/admin/goods/brand') }}" method="get" class="pull-right form-inline page-form" style="margin:0;">
                <input type="hidden" value="1" class="curPage" name="curPage">
                <input class="form-control" name="search" type="text" value="@if(isset($search) && $search != ''){{ $search }}@endif" placeholder="品牌中文名/品牌英文名/简称" style="width:250px;" >
                <button class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i>搜 索
                </button>
            </form>
        </caption>
        <thead>
            <tr>
                <th>排序</th>
                <th>品牌中文名</th>
                <th>品牌英文名</th>
                <th>简称</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
        @if (isset($page))
            @forelse ($page->data as $val)
            <tr>
                <td><input class="form-control" id="s{{ $val->id }}" type="number" onblur="toSort({{ $val->id }},{{ $val->sort}});" value="{{ $val->sort}}" /></td>
                <td><a href="/admin/goods/brand/{{ $val->id }}/edit">{{ $val->logo_cname }}</a></td>
                <td><a href="/admin/goods/brand/{{ $val->id }}/edit">{{ $val->logo_ename }}</a></td>
                <td><a href="/admin/goods/brand/{{ $val->id }}/edit">{{ $val->short_name }}</a></td>
                <td>@if ($val->state==1)<span class="text-success">启用</span>@else 禁用 @endif</td>
                <td>
                    <a href="/admin/goods/brand/{{ $val->id }}/edit"><i class="operator fa fa-edit"></i></a>
                    <a href="javascript:delBrand({{ $val->id }});"><i class="operator fa fa-close"></i></a>
                </td>
            </tr>
            @empty
            @endforelse
        @endif
        </tbody>
    </table>
</div>
@include('include.page')
@include("include.message")
@include('admins.footer')
<script type="text/javascript">
function delBrand(brand_id)
{
    $(this).cnConfirm('确定删除该品牌吗?', function() {
        $.ajax({
            url: '/admin/goods/brand/' + brand_id,
            type: 'delete',
            dataType: 'json',
            success: function(jsonObject) {
                tips(jsonObject.messages);
                if (jsonObject.code == 200) {
                    setTimeout(function(){
                        window.location.href = jsonObject.url;
                    },1000);
                 }
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
            }
        });
    });
}

//排序
function toSort(id,oldSort)
{
    if(id!='') {
        var va = $('#s'+id).val();
        if(va == oldSort) {
            return false;
        }
        var part = /^\d+$/i;
        if(va!='' && va!=undefined && part.test(va)) {
            $.getJSON("/admin/goods/brand/brandSort",{'id':id,'sort':va}, function(data) {
                if (data.code == 200) {
                    $(this).cnAlert(data.messages, 3);
                } else {
                    showErrorNotice(data.messages);
                }
            });
        }
    }
}
</script>
