$(document).ready(function(e) {
    $(this).keydown(function (e){
        if(e.which == "13"){
            $(".login-submit").click();
            $('.register-submit').click();
            $('.findPwd-submit').click();
            $(".changePwd-submit").click();
        }
    })
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    //显示隐藏密码
    $('section').on('click','.iconfont',function(){
        var passwordeye = $(this);
        var showPwd = passwordeye.siblings('input');
        if(passwordeye.hasClass('icon-buxianshimima')){
            passwordeye.removeClass('icon-buxianshimima').addClass('icon-xianshimima');//密码可见
            showPwd.prop('type','text');
        }else{
            passwordeye.removeClass('icon-xianshimima').addClass('icon-buxianshimima');//密码不可见
            showPwd.prop('type','password');
        };
    });
    $.toast.prototype.defaults.duration = 500;//提示锁定时间
    //登录
    $(".login-submit").click(function() {
        var phone = $('#phone').val().trim();
        var oldPassword = $("#password").val();
        var captcha = $('#captcha').val().trim();
    
        if( ! isPoneAvailable(phone)) {
            $.toast("手机号格式错误", "cancel");
            return false;
        }
        if(captcha.length != 4) {
            $.toast("验证码错误", "cancel");
            return false;
        }
        if(oldPassword.length < 6 || oldPassword.length > 20) {
            $.toast("请输入6-20位密码", "cancel");
            return false;
        }
    
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('.pubkey').val());
        var encrypted = encrypt.encrypt(oldPassword);
    
        var buttons = $(this);
        var oldBtnText = buttons.text();
    
        $.ajax({
           url:  '/login/doLogin',
           data: {phone:phone,password:encrypted,captcha:captcha},
           type: 'post',
           dataType: 'json',
           beforeSend: function() {
               buttons.attr('disabled', 'true').text('登录中...');
           },
           success: function(jsonObject) {
               if (jsonObject.code == 200) {
                   $.toast(jsonObject.messages);
                   setTimeout(function(){
                       window.location.href = jsonObject.url;
                   },1000)
               } else {
                   $.toast(jsonObject.messages, "cancel");
                   changeCaptcha();
               }
               buttons.removeAttr('disabled').text(oldBtnText);
           },
           error: function(xhr, type) {
               buttons.removeAttr('disabled').text(oldBtnText);
               changeCaptcha();
               $.toast("登录异常,请重试", "cancel");
           }
        });
    });
    //注册
    $('.register-submit').click(function(){
        var phone = $('#phone').val().trim();
        var captcha = $('#captcha').val().trim();
        var phone_code = $('#phone_code').val().trim();
        var oldPassword = $("#password").val();
        var check_password = $("#check_password").val();
    
        if( ! isPoneAvailable(phone)) {
            $.toast("手机号格式错误", "cancel");
            return false;
        }
        if(captcha.length != 4) {
            $.toast("图形验证码错误", "cancel");
            return false;
        }
        if(phone_code.length < 4) {
            $.toast("手机验证码错误", "cancel");
            return false;
        }
        if (oldPassword != check_password) {
            $.toast("两次密码不一致", "cancel");
            return false;
        }
        if(oldPassword.length < 6) {
            $.toast("密码至少6位", "cancel");
            return false;
        }
    
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('.pubkey').val());
        var encrypted = encrypt.encrypt(oldPassword);
    
        var buttons = $(this);
        var oldBtnText = buttons.text();
    
        $.ajax({
           url:  '/register/doRegister',
           data: {phone:phone,password:encrypted,captcha:captcha,phone_code:phone_code},
           type: 'post',
           dataType: 'json',
           beforeSend: function() {
               buttons.attr('disabled', 'true').text('注册中...');
           },
           success: function(jsonObject) {
               if (jsonObject.code == 200) {
                   $.toast(jsonObject.messages);
                   setTimeout(function(){
                       window.location.href = jsonObject.url;
                   },1000)
               } else {
                   changeCaptcha();
                   $.toast(jsonObject.messages, "cancel");
               }
               buttons.removeAttr('disabled').text(oldBtnText);
           },
           error: function(xhr, type) {
               buttons.removeAttr('disabled').text(oldBtnText);
               $.toast("操作异常,请重试", "cancel");
               changeCaptcha();
           }
        });
    });
    //找回密码
    $('.findPwd-submit').click(function(){
        var phone = $('#phone').val().trim();
        var captcha = $('#captcha').val().trim();
        var phone_code = $('#phone_code').val().trim();
        var oldPassword = $("#password").val();
        var check_password = $("#check_password").val();
    
        if( ! isPoneAvailable(phone)) {
            $.toast("手机号格式错误", "cancel");
            return false;
        }
        if(captcha.length != 4) {
            $.toast("图形验证码错误", "cancel");
            return false;
        }
        if(phone_code.length < 4) {
            $.toast("手机验证码错误", "cancel");
            return false;
        }
        if (oldPassword != check_password) {
            $.toast("两次密码不一致", "cancel");
            return false;
        }
        if(oldPassword.length < 6 || oldPassword.length > 20) {
            $.toast("请输入6-20位密码", "cancel");
            return false;
        }
    
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('.pubkey').val());
        var encrypted = encrypt.encrypt(oldPassword);
    
        var buttons = $(this);
        var oldBtnText = buttons.text();
    
        $.ajax({
           url:  '/login/changePwd',
           data: {phone:phone,password:encrypted,captcha:captcha,phone_code:phone_code},
           type: 'post',
           dataType: 'json',
           beforeSend: function() {
               buttons.attr('disabled', 'true').text('重置中...');
           },
           success: function(jsonObject) {
               if (jsonObject.code == 200) {
                   $.toast(jsonObject.messages);
                   setTimeout(function(){
                       window.location.href = jsonObject.url;
                   },1000)
               } else {
                   changeCaptcha();
                   $.toast(jsonObject.messages, "cancel");
               }
               buttons.removeAttr('disabled').text(oldBtnText);
           },
           error: function(xhr, type) {
               buttons.removeAttr('disabled').text(oldBtnText);
               $.toast("操作异常,请重试", "cancel");
               changeCaptcha();
           }
        });
    });
    //修改密码
    $(".changePwd-submit").click(function() {
        var oldPwd = $("#oldPwd").val();
        var newPwd = $("#newPwd").val();
        var checkPwd = $("#checkPwd").val();
    
        if(oldPwd.length < 6 || oldPwd.length > 20 || newPwd.length < 6 || newPwd.length > 20 || checkPwd.length < 6 || checkPwd.length > 20) {
            $.toast("请输入6-20位密码", "cancel");
            return false;
        }
    
        if(newPwd != checkPwd) {
            $.toast("两次密码不一致", "cancel");
            return false;
        }
    
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('.pubkey').val());
        var oldPwdEncrypted = encrypt.encrypt(oldPwd);
        var newPwdEncrypted = encrypt.encrypt(newPwd);
    
        var buttons = $(this);
        var oldBtnText = buttons.text();
    
        $.ajax({
           url:  '/home/updatePwd',
           data: {oldPwd:oldPwdEncrypted,password:newPwdEncrypted},
           type: 'post',
           dataType: 'json',
           beforeSend: function() {
               buttons.attr('disabled', 'true').text('保存中...');
           },
           success: function(jsonObject) {
               if (jsonObject.code == 200) {
                   $.toast(jsonObject.messages);
                   setTimeout(function(){
                       window.location.href = jsonObject.url;
                   },1000)
               } else {
                   $.toast(jsonObject.messages, "cancel");
               }
               buttons.removeAttr('disabled').text(oldBtnText);
           },
           error: function(xhr, type) {
               buttons.removeAttr('disabled').text(oldBtnText);
               $.toast("系统异常,请重试", "cancel");
           }
        });
    });
    //手机验证码
    $('.weui-vcode-btn').click(function(){
        var phone = $('#phone').val().trim();
        var captcha = $('#captcha').val().trim();
        if( ! isPoneAvailable(phone)) {
            $.toast("手机号格式错误", "cancel");
            return false;
        }    
        if(captcha.length != 4) {
            $.toast("图形验证码错误", "cancel");
            return false;
        }
        var buttons = $(this);
        var oldBtnText = buttons.text();
        var url = buttons.attr('data');
        $.ajax({
            url:  url,
            data: {phone:phone,captcha:captcha},
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                buttons.attr('disabled', 'true').text('发送中...');
            }, 
            success: function(jsonObject) {
                buttons.removeAttr('disabled').text(oldBtnText);
                if (jsonObject.code == 200) {
                    $.toast(jsonObject.messages);
                    
                    //添加cookie记录,有效时间60s
                    if ( url.indexOf('register') >=0 ) {
                        addCookie("registerSecondsremained", 60, 60); 
                        settime($(".weui-vcode-btn"), 'registerSecondsremained'); //开始倒计时
                    } else {
                        addCookie("loginSecondsremained", 60, 60); 
                        settime($(".weui-vcode-btn"), 'loginSecondsremained');
                    }
                } else {
                    $.toast(jsonObject.messages, "cancel");
                    return false;
                }
            },
            error: function(xhr, type) {
                buttons.removeAttr('disabled').text(oldBtnText);
                $.toast("操作异常,请重试", "cancel");
            }
        });
    });
    //是否已形始计时
    var v1 = getCookieValue("registerSecondsremained") ? getCookieValue("registerSecondsremained") : 0;//获取cookie值
    var v2 = getCookieValue("loginSecondsremained") ? getCookieValue("loginSecondsremained") : 0;
    if(v1 > 0) {
        var url = buttons.attr('data');
        if ( url.indexOf('register') >=0 ) {
            settime($(".weui-vcode-btn"), 'registerSecondsremained'); 
        }
    }
    if(v2 > 0) {
        var url = buttons.attr('data');
        if ( url.indexOf('login') >=0 ) {
            settime($(".weui-vcode-btn"), 'loginSecondsremained');
        }
    }
});
//发送验证码时添加cookie
function addCookie(name, value, expiresHours) {
    var cookieString = name + "=" + escape(value);
    //判断是否设置过期时间,0代表关闭浏览器时失效
    if(expiresHours > 0) {
        var date = new Date();
        date.setTime(date.getTime() + expiresHours * 1000);
        cookieString = cookieString + ";expires=" + date.toUTCString();
    }
    document.cookie = cookieString;
}
//修改cookie的值
function editCookie(name, value, expiresHours) {
    var cookieString = name + "=" + escape(value);
    if(expiresHours > 0) {
        var date = new Date();
        date.setTime(date.getTime() + expiresHours * 1000); //单位是毫秒
        cookieString = cookieString + ";expires=" + date.toGMTString();
    }
    document.cookie = cookieString;
}
//根据名字获取cookie的值
function getCookieValue(name) {
    var strCookie = document.cookie;
    var arrCookie = strCookie.split("; ");
    for(var i = 0; i < arrCookie.length; i++) {
        var arr = arrCookie[i].split("=");
        if(arr[0] == name) {
            return unescape(arr[1]);
            break;
        }
    }
}
//开始倒计时
var countdown;
function settime(obj, cookieValue) {
    countdown = getCookieValue(cookieValue);
    var tim = setInterval(function() {
        countdown--;
        obj.attr("disabled", true);
        obj.html("重新发送(" + countdown + ")");
        if(countdown <= 0 ) {
            clearInterval(tim);
            $(obj).removeAttr("disabled");
            $(obj).html("获取验证码");
        }
        editCookie(cookieValue, countdown, countdown + 1);
    }, 1000) //每1000毫秒执行一次
}

function isPoneAvailable(str) {
    var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
    if (!myreg.test(str)) {
        return false;
    } else {
        return true;
    }
}

//切换验证码
function changeCaptcha()
{
    $.get('/getCaptcha', function(data) {
        $('#captchaImg').attr('src', data);
    });
}