@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="{{ url('/admin/home/6') }}">系统</a>
		</li>
		<li>
			<a href="#">权限管理</a>
		</li>
		<li class="active">管理员</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<caption>
			<a class="btn btn-default" href="{{ url('/admin/adminUser/create') }}">
			    <i class="fa fa-plus"></i>添加管理员
			</a>
			<a class="btn btn-default" onclick="selectAll('id[]')">
			    <i class="fa fa-check"></i>全选
			</a>
			<a class="btn btn-default admin-user-list-batch-del">
			    <i class="fa fa-check"></i>批量删除
			</a>
			<form action="{{ url('/admin/adminUser') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="名称/手机号/email/qq/微信" style="width:250px;" >
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<thead>
			<tr>
				<th></th>
				<th>用户名</th>
				<th>角色</th>
				<th>手机号</th>
				<th>Email</th>
				<th>QQ</th>
				<th>微信</th>
				<th>最近登录IP</th>
				<th>最近登录时间</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody class="admin-user-list-tbody">
			@if (isset($page))
				@foreach ($page->data as $data)
					<tr>
						<td>@if ($data->id != 1)<input name="id[]" type="checkbox" value="{{ $data->id }}" />@endif</td>
						<td>
							{{ $data->admin_name }}
						</td>
						<td>
							@if (isset($data->roles) && count($data->roles) > 0)
								@foreach ($data->roles as $role)
									{{ $role->name }},
								@endforeach
							@endif
						</td>
						<td>
							{{ $data->phone }}
						</td>
						<td>
							{{ $data->email }}
						</td>
						<td>
							{{ $data->qq }}
						</td>
						<td>
							{{ $data->we_chat }}
						</td>
						<td>
							{{ $data->last_ip }}
						</td>
						<td>
							{{ $data->last_time }}
						</td>
						<td>
							@if ($data->id != 1 || ($data->id == 1 && $data->id == session('adminUser')->id))<a href="{{ url('/admin/adminUser/' . $data->id . '/edit') }}"><i class='operator fa fa-edit'></i></a>@endif
							@if ($data->id != 1)<a href="javascript:void(0);" class="admin-user-list-del"><i class='operator fa fa-close'></i></a>@endif
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('include.page')
<script src="{{ elixir('js/admins/adminUser.js') }}"></script>
@include('admins.footer')
