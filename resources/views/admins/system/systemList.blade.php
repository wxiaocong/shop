@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="{{ url('/admin/home/6') }}">系统</a>
		</li>
		<li>
			<a href="#">后台首页</a>
		</li>
		<li class="active">系统参数</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<caption>
			<a class="btn btn-default" href="{{ url('/admin/adminUser/create') }}">
			    <i class="fa fa-plus"></i>添加参数
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
				<th>参数名</th>
				<th>参数值</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody class="admin-user-list-tbody">
			@if (isset($page))
				@foreach ($page->data as $data)
					<tr>
						<td>
							{{ $data->name }}
						</td>
						<td>
							{{ $data->val }}
						</td>
                        <td>
                            <a href="{{ url('/admin/adminUser/' . $data->id . '/edit') }}"><i class='operator fa fa-edit'></i></a>
                            <a href="javascript:void(0);" class="admin-user-list-del"><i class='operator fa fa-close'></i></a>
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
