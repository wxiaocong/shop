$(document).ready(function () {
    $(".login-form").validate({
        submitHandler: function(form) {
            var oldPassword = $("input[name='password']").val();
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey($('.pubkey').val());
            var encrypted = encrypt.encrypt($("input[name='password']").val());

            $("input[name='password']").val(encrypted);

            var buttons = $('.login-submit');
            var oldBtnText = buttons.text();
            $.ajax({
                url:  '/admin/login',
                data: $(form).serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    buttons.attr('disabled', 'true').text('登录中...');
                },
                success: function(jsonObject) {

                    if (jsonObject.code == 200) {
                        window.location.href = jsonObject.url;
                    } else {
                        showErrorNotice(jsonObject.messages);
                        $("input[name='password']").val(oldPassword);
                    }
                    buttons.removeAttr('disabled').text(oldBtnText);
                },
                error: function(xhr, type) {
                    ajaxResponseError(xhr, type);
                    buttons.removeAttr('disabled').text(oldBtnText);

                    $("input[name='password']").val(oldPassword);
                }
            });
        },
        // errorPlacement  
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        },
        // rules
        rules: {
            name: {
                required: true
            },
            password: {
                required: true
            },
            captcha: {
                required: true,
                rangelength: [4, 4]
            }
        },
        messages: {
            name: {
                required: "请输入用户名"
            },
            password: {
                required: "请输入密码"
            },
            captcha: {
                required: "请输入验证码",
                rangelength: "验证码长度为{0}个字符！"
            }
        }
    });
});