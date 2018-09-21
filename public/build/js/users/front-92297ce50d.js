$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
$.toast.prototype.defaults.duration = 1000;//提示锁定时间
// 判断是否是微信浏览器的函数
function isWeiXin() {
    // window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
    var ua = window.navigator.userAgent.toLowerCase();
    // 通过正则表达式匹配ua中是否含有MicroMessenger字符串
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}
$(document).ready(function() {
    $('#52Tab').on('click', 'li', function() {
        if (!$(this).hasClass('active')) {
            $.showLoading();
            $.post('/category/getCategoryList', {
                parent_id : $(this).children('a').attr('data')
            }, function(content) {
                $('#52TabContent .row-content').html(content);
                $.hideLoading();
            });
        }
    });
    $('.head-r .icon-gengduo,#headerMask').click(function() {
        $('#headerMask').toggle();
        $('#ulshortcut').toggle();
    });
})
