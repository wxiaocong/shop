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
		<li class="active">商家认定申请列表</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<caption>
			<form action="{{ url('/admin/user/merchantApply') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="昵称/手机号码" >
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<colgroup>
			<col width="80px" />
			<col width="80px" />
			<col width="100px" />
			<col width="120px" />
			<col width="50px" />
			<col width="80px" />
		</colgroup>
		<thead>
			<tr>
				<th>昵称</th>
				<th>手机号</th>
				<th>公司名称</th>
				<th>公司地址</th>
				<th>店铺工位</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			@if (isset($page))
				@foreach ($page->data as $data)
				<tr>
					<td>{{ $data->nickname }}</td>
					<td>{{ $data->mobile }}</td>
					<td>{{ $data->company_name }}</td>
					<td>
						@if (isset($data->companyProvinceObj))
							{{ $data->companyProvinceObj->area_name }}&nbsp;
						@endif
						@if (isset($data->companyCityObj))
							{{ $data->companyCityObj->area_name }}&nbsp;
						@endif
						@if (isset($data->companyAreaObj))
							{{ $data->companyAreaObj->area_name }}&nbsp;
						@endif
						{{ $data->company_address }}
					</td>
					<td>{{ $data->shop_site }}</td>
					<td>
						<a target="_blank" href="{{ url('/admin/user/' . $data->id) }}"><button class="btn btn-sm btn-primary">去审核</button></a>
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
