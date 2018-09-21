$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//切换验证码
function changeCaptcha()
{
    $.get('/getCaptcha', function(data) {
        $('#captchaImg').attr('src', data);
    });
}

//全选
function selectAll(val)
{
    if($("input[type='checkbox'][name^='"+val+"']:not(:checked)").length > 0)
    {
        $("input[type='checkbox'][name^='"+val+"']").prop('checked',true);
    }
    else
    {
        $("input[type='checkbox'][name^='"+val+"']").prop('checked',false);
    }
}

/**
 * 删除操作
 * @param object conf
       msg :提示信息;
       form:要提交的表单名称;
       link:要跳转的链接地址;
 */
function delObject(conf)
{
    var ok = null;            //执行操作
    var msg= '确定要删除么？';//提示信息

    if(conf)
    {
        if(conf.form)
        {
            var ok = 'formSubmit("'+conf.form+'")';
            if(conf.link)
            {
                var ok = 'formSubmit("'+conf.form+'","'+conf.link+'")';
            }
        }
        else if(conf.link)
        {
            var ok = 'window.location.href="'+conf.link+'"';
        }

        if(conf.msg)
        {
            var msg = conf.msg;
        }

        if(conf.name && checkboxCheck(conf.name,"请选择要操作项") == false)
        {
            return '';
        }
    }
    if(ok==null && document.forms.length >= 1)
        var ok = 'document.forms[0].submit();';

    if(ok!=null)
    {
        window.confirm(msg,ok);
    }
    else
    {
        alert('删除操作缺少参数');
    }
}

//根据表单的name值提交
function formSubmit(formName,url)
{
    if(url)
    {
        $('form[name="'+formName+'"]').attr('action',url);
    }
    $('form[name="'+formName+'"]').submit();
}

//根据checkbox的name值检测checkbox是否选中
function checkboxCheck(boxName,errMsg)
{
    if($('input[name="'+boxName+'"]:checked').length < 1)
    {
        alert(errMsg);
        return false;
    }
    return true;
}

//倒计时
var countdown=function()
{
    var _self=this;
    this.handle={};
    this.parent={'second':'minute','minute':'hour','hour':""};
    this.add=function(id)
    {
        _self.handle.id=setInterval(function(){_self.work(id,'second');},1000);
    };
    this.work=function(id,type)
    {
        if(type=="")
        {
            return false;
        }

        var e = document.getElementById("cd_"+type+"_"+id);
        var value=parseInt(e.innerHTML);
        if( value == 0 && _self.work( id,_self.parent[type] )==false )
        {
            clearInterval(_self.handle.id);
            return false;
        }
        else
        {
            e.innerHTML = (value==0?59:(value-1));
            return true;
        }
    };
};

// error info notice modal ** begin
var errorNoticeModal = $("#error-notice-modal");
var infoContent = errorNoticeModal.find("ul");

errorNoticeModal.on("hidden.bs.modal", function() {
    infoContent.html("");
});

function showErrorNotice(errorMessages) {
    infoContent.find("li").remove();
    for (var i = 0; i < errorMessages.length; i++) {
        var html = "<li>" + errorMessages[i] + "</li>";
        infoContent.append(html);
    };
    errorNoticeModal.modal("show");
}
// error info notice modal ** end

// ajax submit after abnormal
function ajaxResponseError(xhr, type) {
    if (xhr.status == 422) {
        showErrorNotice(xhr.responseJSON);
    } else if (xhr.status == 401) {
        window.location.href = '/errors/401';
    } else {
        showErrorNotice(["系统异常，操作失败，错误码:" + xhr.status]);
    }
}

// cnConfirm 
$.fn.extend({
    cnConfirm: function(message, callback) {
        var div = $('#confirm-message');
        
        div.find(".modal-body").html(message);
        div.modal('show');
        div.find(".modal-footer").find(".btn-confirm").unbind('click');
        div.find(".modal-footer").find(".btn-confirm").one("click", function() {
            div.modal('hide');
            if (typeof(callback) != 'undefined' && callback != null && callback != "") {
                callback();
            }
        });
    }
});

// cnAlert
$.fn.extend({
    cnAlert: function(message, second, route) {
        var s = 5;
        var div = $('#alert-message');
        div.find(".modal-body").html(message);
        div.modal('show');
        if (typeof(second) != 'undefined' && second != null && second != "") {
            s = second;
        }
        div.find(".modal-footer").find(".btn-sm").one("click", function() {
            div.modal('hide');
            if (typeof(route) != 'undefined' && route != null && route != "") {
                window.location.href = route;
            }
        });
        window.setTimeout(function() {
            if (typeof(redirectUrl) != 'undefined' && redirectUrl != null && redirectUrl != "") {
                window.location.href = redirectUrl;
            }
            
            div.modal('hide');
            if (typeof(route) != 'undefined' && route != null && route != "") {
                window.location.href = route;
            }
        }, second * 1000);
    }
});

/**
 * @brief 点击分页操作
 * @param int page
 */
function pageHref(page)
{
    $('.page-form').find('.curPage').val(page);
    $('.page-form').submit();
}

window.loadding = function(message){var message = message ? message : '正在执行，请稍后...';art.dialog({"id":"loadding","lock":true,"fixed":true,"drag":false}).content(message);}
window.unloadding = function(){art.dialog({"id":"loadding"}).close();}
window.tips = function(mess){art.dialog.tips(mess);}
window.alert = function(mess){art.dialog.alert(String(mess));}


function onlyAmount(th) {
    var a = [
        ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
        ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
        ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
        ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
    ];
    for(i = 0; i < a.length; i++) {
        var reg = new RegExp(a[i][0]);
        th.value = th.value.replace(reg, a[i][1]);
    }

    var index = th.value.indexOf('-');
    if(index != -1) {
        if (index == (th.value.length - 1)) {
            th.value = th.value.substr(0, index);
        } else if (index == 0) {
            th.value = th.value.substr(1);
        } else {
            var value = th.value.substr(0, index);
            th.value = value + th.value.substr(index+1);
        }
    }
}

//只能输入数字
function onlyNum(t) {
    t.value = t.value.replace(/[^\d]/g, '');
}