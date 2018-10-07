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
        <li class="active">编辑代理类型</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <form method="post" action="{{ url('admin/agentType') }}" class="edit-admin-user-form">
        <textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
        <input class="type_id" name="id" type="hidden" value="{{ $param->id ?? '' }}" />
        <table class="table form-table">
            <tr>
                <th>类型名称：</th>
                <td><input type="text" class="form-control" name="type_name" value="{{ $param->type_name ?? '' }}"/></td>
            </tr>
            <tr>
                <th>价格：</th>
                <td><input type="text" class="form-control" name="price" value="{{ isset($param->price) ? sprintf("%.2f", $param->price/100) : '' }}"/></td>
            </tr>
            <tr>
                <th>返利：</th>
                <td><input type="text" class="form-control" name="returnMoney" value="{{ isset($param->returnMoney) ? sprintf("%.2f", $param->returnMoney/100) : '' }}"/></td>
            </tr>
            <tr>
                <th>配货数量：</th>
                <td><input type="text" class="form-control" name="goodsNum" value="{{ $param->goodsNum ?? '' }}"/></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="btn btn-primary admin-user-submit" type="button">保存</button>
                </td>
            </tr>
        </table>
    </form>
</div>
@include('include.message')
@include('admins.footer')
<script>
$('.admin-user-submit').click(function() {
    $('.edit-admin-user-form').submit();
});
$('.edit-admin-user-form').validate({
    submitHandler: function(form) {
        var buttons = $('.admin-user-submit');
        var oldBtnText = buttons.text();
        var id = $('.type_id').val();
        var action = id == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/agentType/' + id : '/admin/agentType';
        $.ajax({
            url:  url,
            data: $(form).serialize(),
            type: action,
            dataType: 'json',
            beforeSend: function() {
                buttons.attr('disabled', 'true').text('提交中...');
            },
            success: function(jsonObject) {
                if (jsonObject.code == 200) {
                    window.location.href = jsonObject.url;
                } else {
                    showErrorNotice(jsonObject.messages);
                }
                buttons.removeAttr('disabled').text(oldBtnText);
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
                buttons.removeAttr('disabled').text(oldBtnText);
            }
        });
    },
    // errorPlacement  
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
    // rules
    rules: {
        type_name: {
            required: true,
            maxlength: 100
        }
    },
    messages: {
        name: {
            required: '代理类型不能为空',
            maxlength: '代理类型最多100个字符'
        }
    }
});
</script>