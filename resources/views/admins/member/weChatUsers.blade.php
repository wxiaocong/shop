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
		<li class="active">微信访问用户列表</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<colgroup>
			<col width="140px" />
			<col width="80px" />
			<col width="80px" />
			<col width="160px" />
			<col />
			<col width="100px" />
			<col width="160px" />
			<col width="160px" />
			<col width="160px" />
			<col width="150px" />
		</colgroup>
		<caption>
			<form action="{{ url('/admin/user/wechatUsers') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				是否绑定注册用户<select class="form-control" name="isBind">
					<option value="0">选择</option>
					<option value="1" @if($isBind == 1) selected @endif>是</option>
					<option value="2" @if($isBind == 2) selected @endif>否</option>
				</select>
				&nbsp;&nbsp;<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="昵称" >
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<thead>
			<tr>
				<th>昵称</th>
				<th>性别</th>
				<th>是否关注</th>
				<th>关注时间</th>
				<th>所在地区</th>
				<th>是否绑定注册用户</th>
				<th>绑定时间</th>
				<th>解绑时间</th>
				<th>最后访问时间</th>
				<th>最后访问IP</th>
			</tr>
		</thead>
		<tbody>
			@if (isset($page))
				@foreach ($page->data as $data)
				<tr>
					<td>{{ $data->nickname }}</td>
					<td>@if ($data->sex == 1) 男 @elseif ($data->sex == 2) 女 @else 未知 @endif</td>
					<td>@if ($data->subscribe == 1) 是 @else 否 @endif</td>
					<td>@if (!empty($data->subscribe_time)) {{ date('Y-m-d H:i:s', $data->subscribe_time) }} @endif</td>
					<td>{{ $data->province }}&nbsp;{{ $data->city }}</td>
					<td>@if (isset($data->user)) 是 @else 否 @endif</td>
					<td>{{ $data->bind_time }}</td>
					<td>{{ $data->unbind_time }}</td>
					<td>{{ $data->last_time }}</td>
					<td>{{ $data->last_ip }}</td>
				</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('include.page')
@include('admins.footer')
