
@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="#">系统</a>
		</li>
		<li>
			<a href="#">权限管理</a>
		</li>
		<li class="active">修改登录密码</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form class="admin-edit-pwd-form">
		<textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
		<table class="table form-table">
			<colgroup>
				<col width="130px" />
				<col />
			</colgroup>
            <tr>
                <th>原密码：</th>
                <td>
                    <input type="password" id="oldPassword" class="form-control" name="oldPassword"/>
                </td>
            </tr>
			<tr>
				<th>密码：</th>
				<td>
					<input type="password" id="password" class="form-control" name="password"/>
					<p class="help-block">* 管理员登录后台的密码，请填写英文字母，数字或下划线，在6-32个字符之间</p>
				</td>
			</tr>
			<tr>
				<th>重复密码：</th>
				<td>
					<input type="password" id="repassword" class="form-control" name="repassword"/>
					<p class="help-block">* 重复输入管理员登录后台的密码</p>
				</td>
			</tr>
			<tr><td></td><td><button class="btn btn-primary admin-edit-pwd-submit" type="button">保存</button></td></tr>
		</table>
	</form>
</div>
@include('include.message')
<script src="{{ asset('lib/jsencrypt/jsencrypt.min.js') }}"></script>
<script type="text/javascript">
$('.admin-edit-pwd-submit').click(function() {
    $('.admin-edit-pwd-form').submit();
});
$('.admin-edit-pwd-form').validate({
    submitHandler: function(form) {
    	var password = $("input[name='password']").val();
        var repassword = $("input[name='repassword']").val();
        var oldPassword = $("input[name='oldPassword']").val();

        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('.pubkey').val());
        var encrypted = encrypt.encrypt(password);
        $("input[name='password']").val(encrypted);
        $("input[name='repassword']").val(encrypted);
        $("input[name='oldPassword']").val(encrypt.encrypt(oldPassword));

        var buttons = $('.admin-edit-pwd-submit');
        var oldBtnText = buttons.text();
        $.ajax({
            url: '/admin/adminUser/updatePwd',
            data: $(form).serialize(),
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                buttons.attr('disabled', 'true').text('提交中...');
            },
            success: function(jsonObject) {
                if (jsonObject.code == 200) {
                    $(this).cnAlert(jsonObject.messages, 3);
                    setTimeout(function(){
                        window.location.href = jsonObject.url;
                    },3000);
                } else {
                    showErrorNotice(jsonObject.messages);

                    $("input[name='password']").val(password);
                    $("input[name='repassword']").val(repassword);
                    $("input[name='oldPassword']").val(oldPassword);
                }
                buttons.removeAttr('disabled').text(oldBtnText);
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
                buttons.removeAttr('disabled').text(oldBtnText);

                $("input[name='password']").val(password);
                $("input[name='repassword']").val(repassword);
                $("input[name='oldPassword']").val(oldPassword);
            }
        });
    },
    // errorPlacement
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
    // rules
    rules: {
        oldPassword: {
            required: true,
            minlength: 6
        },
        password: {
            required: true,
            minlength: 6
        },
        repassword: {
            equalTo:"#password"
        }
    },
    messages: {
        oldPassword: {
            required: "请输入原密码",
            minlength: "密码最少6位长度！"
        },
        password: {
            required: "请输入新密码",
            minlength: "密码最少6位长度！"
        },
        repassword: {
            equalTo:"两次密码输入不一致"
        }
    }
});
</script>
@include('admins.footer')
