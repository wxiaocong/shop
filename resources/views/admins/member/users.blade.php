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
		<li class="active">用户列表</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<colgroup>
			<col width="140px" />
			<col width="120px" />
			<col width="80px" />
			<col />
			<col width="100px" />
			<col width="100px" />
			<col width="160px" />
			<col width="130px" />
			<col width="100px" />
			<col width="80px" />
			<col width="80px" />
		</colgroup>
		<caption>
			<form action="{{ url('/admin/user') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				是否商家用户<select class="form-control" name="isBusiness">
					<option value="0">选择</option>
					<option value="1" @if($isBusiness == 1) selected @endif>商家用户</option>
					<option value="2" @if($isBusiness == 2) selected @endif>个人用户</option>
				</select>&nbsp;&nbsp;
				是否绑定微信<select class="form-control" name="isBind">
					<option value="0">选择</option>
					<option value="1" @if($isBind == 1) selected @endif>是</option>
					<option value="2" @if($isBind == 2) selected @endif>否</option>
				</select>
				&nbsp;&nbsp;<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="昵称/手机号码" >
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<thead>
			<tr>
				<th>昵称</th>
				<th>手机号</th>
				<th>性别</th>
				<th>联系地址</th>
				<th>余额</th>
				<th>是否商家用户</th>
				<th>最后登录时间</th>
				<th>最后登录IP</th>
				<th>是否绑定微信</th>
				<th>状态</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			@if (isset($page))
				@foreach ($page->data as $data)
				<tr>
					<td>{{ $data->nickname }}</td>
					<td><a href="{{ url('/admin/user/' . $data->id) }}">{{ $data->mobile }}</a></td>
					<td>@if ($data->sex == 1) 男 @elseif ($data->sex == 2) 女 @else 未知 @endif</td>
					<td>
						@if (isset($data->provinceObj))
							{{ $data->provinceObj->area_name }}&nbsp;
						@endif
						@if (isset($data->cityObj))
							{{ $data->cityObj->area_name }}&nbsp;
						@endif
						@if (isset($data->areaObj))
							{{ $data->areaObj->area_name }}&nbsp;
						@endif
						{{ $data->address }}
					</td>
					<td>{{ round($data->balance/100,2) }}</td>
					<td>@if ($data->is_business == 1) 商家用户 @else 个人用户 @endif</td>
					<td>{{ $data->last_time }}</td>
					<td>{{ $data->last_ip }}</td>
					<td>@if (isset($data->wechatUser)) 是 @else 否 @endif</td>
					<td>
						<input type="hidden" value="{{ $data->id }}" />
						<select class="input-sm user-list-state">
							<option value="1" @if($data->state == 1) selected @endif>正常</option>
							<option value="2" @if($data->state == 2) selected @endif>锁定</option>
						</select>
					</td>
					<td><a href="{{ url('/admin/user/' . $data->id) }}"><i class='operator fa fa-file-text-o fa-lg'></i></a></td>
				</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('include.page')
<script type="text/javascript">
//正常/锁定
$('.list-table').on('change', '.user-list-state', function(event) {
	var id = $(this).prev().val();
	$.ajax({
        url:  '/admin/user/' + id + '/updateState',
        type: 'post',
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                $(this).cnAlert(jsonObject.messages, 3);
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
});
</script>
@include('admins.footer')
