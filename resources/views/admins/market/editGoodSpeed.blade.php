@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="/admin/home/4">营销</a>
		</li>
		<li>
			<a href="/admin/speed">营销活动管理</a>
		</li>
		<li class="active">编辑限时抢购</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form class="edit-good-speed-form">
		<input type="hidden" class="good-speed-id" name="id" value="@if(isset($speed)) {{ $speed->id }} @endif"/>
		<table class="table form-table">
			<colgroup>
				<col width="130px" />
				<col />
			</colgroup>

			<tr>
				<th>限时抢购名称：</th>
				<td>
					@if (isset($operationType) && ($operationType == 'editIsClose' || $operationType == 'query'))
						{{ $speed->name }}
						<input type="hidden" name="name" value="{{ $speed->name }}"/>
					@else
						<input type="text" class="form-control" name="name" value="@if(isset($speed)) {{ $speed->name }} @endif"/>
					@endif
					<p class="help-block">* 名称建议包含商品名称、时间、抢购数量、抢购价格等信息，以便查看列表时一目了然</p>
				</td>
			</tr>
			<tr>
				<th>限时抢购时间：</th>
				<td>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="input-group">
                            	<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                <input class="form-control datepicker" type="text" name="startDate" value="@if(isset($speed)) {{ $speed->start_time }} @endif" placeholder="起始日期" @if (isset($operationType) && ($operationType == 'editIsClose' || $operationType == 'query')) readonly @endif/>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                <input class="form-control datepicker" type="text" name="endDate" value="@if(isset($speed)) {{ $speed->end_time }} @endif" placeholder="结束日期" @if (isset($operationType) && ($operationType == 'editIsClose' || $operationType == 'query')) readonly @endif/>
                            </div>
                        </div>
                    </div>
				</td>
			</tr>
			<tr>
				<th>是否开启：</th>
				<td>
                    @if (isset($operationType) && $operationType == 'query')
						@if ($speed->is_close != 1)
							是
						@else
							否
						@endif
						<input type="hidden" name="isClose" value="{{ $speed->is_close }}"/>
					@else
						<label class="radio-inline">
	                        <input type="radio" name="isClose" value="0" @if(!isset($speed) || $speed->is_close != 1) checked @endif >是
	                    </label>
	                    <label class="radio-inline">
	                        <input type="radio" name="isClose" value="1" @if(isset($speed) && $speed->is_close == 1) checked @endif>否
	                    </label>
					@endif
				</td>
			</tr>
			<tr>
				<th>设置抢购商品：</th>
				<td>
					<table class="table table-bordered list-table" >
						<colgroup>
							<col width="100px" />
							<col />
							<col width="160px" />
							<col width="80px" />
							<col width="110px" />
							<col width="70px" />
							<col width="120px" />
							<col width="130px" />
						</colgroup>
						<thead>
							<tr>
								<td>图片</td>
								<td>名称</td>
								<td>品牌</td>
								<td>原价格</td>
								<td>限时抢购价格</td>
								<td>库存</td>
								<td>限时抢购总数量</td>
								<td>单次最大购买数量</td>
							</tr>
						</thead>
						<tbody>
							@if(isset($speed) && isset($awardValue) && isset($spec))
								<tr><td><img src="{{ $awardValue->img }}" width="45px" /><input type="hidden" name="specId" value="{{ $awardValue->id }}"/></td>
								<td>{{ $awardValue->name }}</td>
								<td>{{ $spec->brand->short_name }}</td>
								<td>{{ round($spec->sell_price/100, 2) }}</td>
								@if (isset($operationType) && ($operationType == 'editIsClose' || $operationType == 'query'))
									<td>{{ round($awardValue->price/100, 2) }}<input type="hidden" name="price" value="{{ round($awardValue->price/100, 2) }}"/></td>
									<td>{{ $spec->number }}</td>
									<td>{{ $awardValue->totalNum }}<input type="hidden" name="totalNum" value="{{ $awardValue->totalNum }}"/></td>
									<td>{{ $awardValue->onceNum }}<input type="hidden" name="onceNum" value="{{ $awardValue->onceNum }}"/></td>
								@else
									<td><input type="text" class="form-control" name="price" onkeyup="onlyAmount(this)" placeholder="请填写数字" value="{{ round($awardValue->price/100, 2) }}"/></td>
									<td>{{ $spec->number }}</td>
									<td><input type="text" class="form-control" name="totalNum" onkeyup="onlyNum(this)" placeholder="请填写数字" value="{{ $awardValue->totalNum }}"/></td>
									<td><input type="text" class="form-control" name="onceNum" onkeyup="onlyNum(this)" placeholder="请填写数字" value="{{ $awardValue->onceNum }}"/></td>
								@endif
								</tr>
							@endif
							<tr class="speed-goods">
								<td colspan="8">
									<button type="button" onclick="searchGoods(this);" class="btn btn-default" @if (isset($operationType) && ($operationType == 'editIsClose' || $operationType == 'query')) disabled @endif><span>选择商品</span></button>
									<p class="help-block">* 设置要限时抢购的商品</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th>描述：</th>
				<td><textarea class="form-control" rows="3" name="description" @if (isset($operationType) && ($operationType == 'editIsClose' || $operationType == 'query')) readonly @endif>@if(isset($speed)) {{ $speed->intro }} @endif</textarea></td>
			</tr>
			<tr><td></td><td><button class="btn btn-primary good-speed-submit" type="submit">确定</button></td></tr>
		</table>
	</form>
</div>
<div class="hidden search-good-template">
	<div class="container" style="min-width:420px;margin-top:10px;">
		<form action="/admin/speed/findGoods" method="get" class="search-good-form">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>商品名称</span></button>
					</div>
					<input type="text" class="form-control" name="search" placeholder="商品名称/编号（料号）/条形码" onkeydown="if(event.keyCode==13){return false;}">
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>商品品牌</span></button>
					</div>
					<select class="form-control" name="brandId">
						<option value="0">选择品牌</option>
						@foreach ($brandList as $brand)
							<option value="{{ $brand->id }}">{{ $brand->short_name }}</option>
						@endforeach
					</select>
				</div>
			</div>
		</form>
	</div>
</div>
@include('include.message')
<script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ elixir('js/common/datetimePicker.js') }}"></script>
<script src="{{ elixir('js/admins/goodSpeed.js') }}"></script>
@include('admins.footer')
