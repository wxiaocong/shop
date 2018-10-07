@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="#">用户</a>
        </li>
        <li>
            <a href="#">用户管理</a>
        </li>
        <li class="active">代理商申请列表</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <table class="table list-table">
        <caption>
            <form action="{{ url('/admin/agent') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
                <input type="hidden" value="1" class="curPage" name="curPage">
                <input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="姓名/手机号码" >
                <button class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i>搜 索
                </button>
            </form>
        </caption>
        <thead>
            <tr>
                <th>订单号</th>
                <th>用户昵称</th>
                <th>代理商姓名</th>
                <th>推荐人</th>
                <th>合作类型</th>
                <th>地区</th>
                <th>订单金额</th>
                <th>手机号</th>
                <th>付款时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($page))
                @foreach ($page->data as $data)
                <tr>
                    <td>{{ $data->order_sn }}</td>
                    <td>{{ $data->nickname }}</td>
                    <td>{{ $data->agent_name }}</td>
                    <td>{{ $data->referee_name }}</td>
                    <td>{{ $agent[$data->level]['type_name'] }}</td>
                    <td>@if (isset($data->provinceObj))
                            {{ $data->provinceObj->area_name }}&nbsp;
                        @endif
                        @if (isset($data->cityObj))
                            {{ $data->cityObj->area_name }}&nbsp;
                        @endif
                        @if (isset($data->areaObj))
                            {{ $data->areaObj->area_name }}&nbsp;
                        @endif
                    </td>
                    <td>{{sprintf("%.2f", $data->payment/100)}}</td>
                    <td>{{ $data->mobile }}</td>
                    <td>{{ $data->pay_time }}</td>
                    <td>{{ $agentState[$data->state] }}</td>
                    <td>
                        <a target="_blank" href="{{ url('/admin/agent/' . $data->id) }}">
                            <button class="btn btn-sm btn-primary">@if($data->state == 2) 去审核 @else 查看 @endif</button>
                        </a>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@include('include.message')
@include('include.page')
@include('admins.footer')
