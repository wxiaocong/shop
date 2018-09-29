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
        <li class="active">编辑系统参数</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
    <form method="post" action="{{ url('admin/system') }}" class="edit-admin-user-form">
        <textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
        <input class="system_id" name="id" type="hidden" value="{{ $param->id ?? '' }}" />
        <table class="table form-table">
            <tr>
                <th>参数名：</th>
                <td><input type="text" class="form-control" name="name" value="{{ $param->name ?? '' }}"/></td>
            </tr>
            <tr>
                <th>参数值：</th>
                <td><input type="text" class="form-control" name="val" value="{{ $param->val ?? '' }}"/></td>
            </tr>
            <tr>
                <th>描述：</th>
                <td><input type="text" class="form-control" name="desc" value="{{ $param->desc ?? '' }}"/></td>
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
        var id = $('.system_id').val();
        var action = id == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/system/' + id : '/admin/system';
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
        name: {
            required: true,
            maxlength: 60
        }
    },
    messages: {
        name: {
            required: '参数名不能为空',
            maxlength: '参数名最多60个字符'
        }
    }
});
</script>