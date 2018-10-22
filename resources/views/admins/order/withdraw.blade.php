@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="#">订单</a>
        </li>
        <li>
            <a href="#">提现单管理</a>
        </li>
        <li class="active">提现单列表</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <table class="table list-table">
        <caption>
            <form action="{{ url('/admin/withdraw') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
                <input type="hidden" value="1" class="curPage" name="curPage">
                申请时间&nbsp;<input class="form-control datepicker" type="text" name="startPayDate" value="@if(isset($startPayDate) && $startPayDate != '') {{ $startPayDate }} @endif" placeholder="起始日期" style="width: 100px;" /> - <input class="form-control datepicker" type="text" name="endPayDate" value="@if(isset($endPayDate) && $endPayDate != '') {{ $endPayDate }} @endif" placeholder="结束日期" style="width: 100px;" />
                &nbsp;&nbsp;订单状态&nbsp;<select class="form-control" name="state">
                    <option value="">选择订单状态</option>
                        <option value="1" @if($state == 1) selected @endif>等待付款</option>
                        <option value="2" @if($state == 2) selected @endif>已付款</option>
                        <option value="3" @if($state == 3) selected @endif>取消</option>
                </select>
                &nbsp;&nbsp;<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="订单编号/申请用户" style="width: 250px;">
                <button class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i>搜 索
                </button>
            </form>
        </caption>
        <thead>
            <tr>
                <th>订单编号</th>
                <th>申请用户</th>
                <th>提现金额</th>
                <th>申请时间</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($page))
                @foreach ($page->data as $data)
                <tr>
                    <td><a target="_blank" href="{{ url('/admin/withdraw/' . $data->id) }}">{{ $data->order_sn }}</a></td>
                    <td>{{ $data->nickname}}({{$data->realname}})</td>
                    <td>{{ sprintf("%.2f",$data->amount/100) }}</td>
                    <td>{{ $data->created_at }}</td>
                    <td>@if($data->state==1) 等待付款 @elseif($data->state==2) 提现成功 @else 取消 @endif</td>
                    <td>
                        <a target="_blank" href="{{ url('/admin/withdraw/' . $data->id) }}"><i class='operator fa fa-file-text-o fa-lg' title="查看订单"></i></a>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@include('include.message')
@include('include.page')
<script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ elixir('js/common/datetimePicker.js') }}"></script>
@include('admins.footer')