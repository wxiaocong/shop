@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="#">订单</a>
        </li>
        <li class="active">资金记录</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <table class="table list-table">
        <caption>
            <form action="{{ url('/admin/payLog') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
                <input type="hidden" value="1" class="curPage" name="curPage">
                时间&nbsp;<input class="form-control datepicker" type="text" name="startPayDate" value="@if(isset($startPayDate) && $startPayDate != '') {{ $startPayDate }} @endif" placeholder="起始日期" style="width: 100px;" /> - <input class="form-control datepicker" type="text" name="endPayDate" value="@if(isset($endPayDate) && $endPayDate != '') {{ $endPayDate }} @endif" placeholder="结束日期" style="width: 100px;" />
                &nbsp;&nbsp;类型&nbsp;<select class="form-control" name="payType">
                        <option value="">选择类型</option>
                        @foreach($type as $key=>$val)
                        <option value="{{$key}}" @if($key == $payType) selected @endif>{{$val}}</option>
                        @endforeach
                </select>
                &nbsp;&nbsp;<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="用户" style="width: 250px;">
                <button class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i>搜 索
                </button>
            </form>
        </caption>
        <thead>
            <tr>
                <th>时间</th>
                <th>用户</th>
                <th>支出</th>
                <th>收入</th>
                <th>类型</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($page))
                @foreach ($page->data as $data)
                <tr>
                    <td>{{$data->created_at}}</td>
                    <td>{{$data->nickname}}</td>
                    <td>@if($data->pay_type == 1) 0.00 @else{{sprintf("%.2f",$data->gain/100)}} @endif</td>
                    <td>{{sprintf("%.2f",$data->expense/100)}}</td>
                    <td>{{ $type[$data->pay_type] }}</td>
                </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="2" style="text-align:right;">合计:</td>
                <td>{{sprintf("%.2f",($page->total->total_gain??0)/100)}}</td>
                <td>{{sprintf("%.2f",($page->total->total_expense??0)/100)}}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
@include('include.message')
@include('include.page')
<script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ elixir('js/common/datetimePicker.js') }}"></script>
@include('admins.footer')
