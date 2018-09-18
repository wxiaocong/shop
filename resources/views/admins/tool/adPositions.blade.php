@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="#">工具</a>
        </li>
        <li>
            <a href="#">广告管理</a>
        </li>
        <li class="active">首页幻灯片</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <form class="page-form ad-position-form" action="{{ url('/admin/ad') }}">
        <table class="table list-table">
            <colgroup>
                <col width="35px" />
                <col />
                <col width="150px" />
                <col />
                <col width="100px" />
            </colgroup>
            <caption>
                <a class="btn btn-default" href="{{ url('/admin/ad/create') }}">
                    <i class="fa fa-plus"></i>添加首页幻灯片
                </a>
                <a class="btn btn-default" onclick="selectAll('id[]')">
                    <i class="fa fa-check"></i>全选
                </a>
                <a class="btn btn-default ad-position-batch-del">
                    <i class="fa fa-close"></i>批量删除
                </a>
                <input type="hidden" value="1" class="curPage" name="curPage">
            </caption>
            <thead>
                <tr>
                    <th></th>
                    <th>幻灯片标题</th>
                    <th>图片</th>
                    <th>URL</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($page))
                    @foreach ($page->data as $data)
                        <tr>
                            <td><input name="id[]" type="checkbox" value="{{ $data->id }}" /></td>
                            <td><a href="{{ url('/admin/ad/' . $data->id . '/edit') }}">{{ $data->title }}</a></td>
                            <td><img src="{{ $data->img . '?x-oss-process=image/resize,w_120,h_60' }}" style="width:120px;height:60px" /></td>
                            <td>{{ $data->url }}</td>
                            <td>
                                <a href="{{ url('/admin/ad/' . $data->id . '/edit') }}"><i class='operator fa fa-edit'></i></a>
                                <a href="javascript:void(0)" class="ad-position-del"><input type="hidden" value="{{ $data->id }}" /><i class='operator fa fa-close'></i></a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </form>
</div>
@include('include.page')
@include('include.message')
<script src="{{ elixir('js/admins/adPosition.js') }}"></script>
@include('admins.footer')
