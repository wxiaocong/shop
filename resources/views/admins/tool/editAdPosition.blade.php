@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/7">工具</a>
        </li>
        <li>
            <a href="/admin/ad">广告管理</a>
        </li>
        <li class="active">更新首页幻灯片</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <form class="edit-ad-position-form" action="{{ url('/admin/ad') }}">
        <input class="ad-position-id" name="id" type="hidden" value="{{ isset($data) ? $data->id : "" }}" />
        <table class="table form-table">
            <colgroup>
                <col width="130px" />
                <col />
            </colgroup>
            <tr>
                <th>标题：</th>
                <td>
                    <input class="form-control" name="title" type="text" value="{{ isset($data) ? $data->title : "" }}"  />
                </td>
            </tr>
            <tr>
                <th>图片对应url：</th>
                <td>
                    <input class="form-control" name="url" type="text" value="{{ isset($data) ? $data->url: "" }}" placeholder="请填写正确的图片url" />
                </td>
            </tr>
            <tr>
                <th>图片：</th>
                <td>*+
                    @if (isset($data))
                        @if (strpos($data->img, 'http') === 0)
                            <img class="img-upload" src="{{ $data->img }}" name="img" style="width: 280px"/>
                        @else
                            <img class="img-upload" src="{{ url('/') }}/{{ $data->img }}" name="img" style="width: 120px"/>
                        @endif
                    @else
                        <img class="img-upload" name="img" style="width: 280px"/>
                    @endif
                    <p class="help-block">建议高度180px,宽度和高度比例为2：1</p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button class='btn btn-primary ad-position-submit' type="submit">保存</button></td>
            </tr>
        </table>
    </form>
</div>
@include('include.message')
<script src="{{ asset('js/common/fileinput/js/plugins/sortable.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/fileinput.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/locales/zh.js') }}"></script>
<script src="{{ asset('js/common/fileinput/themes/explorer/theme.js') }}"></script>
<script src="{{ elixir('js/common/fileUpload.js') }}"></script>
<script src="{{ elixir('js/admins/adPosition.js') }}"></script>
@include('admins.footer')
