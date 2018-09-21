$(document).ready(function () {
	// left sub menu selected add the class
    var selectedSubMenuId = getCookie('selectedSubMenu');
    $('ul.sidebar-menu ul.treeview-menu li').each(function(index) {
        if (selectedSubMenuId == null) {
            return;
        }

        var subMenuClass = $(this).attr('class');
        subMenuClass = subMenuClass.split("-");
        var id = subMenuClass[3];

        if (selectedSubMenuId == id) {
            $(this).addClass("active").parent('ul').show().parent('.treeview').addClass('menu-open');
        }
    });

    $('ul.sidebar-menu ul.treeview-menu li').click(function() {
        var subMenuClass = $(this).attr('class');
        subMenuClass = subMenuClass.split("-");
        var id = subMenuClass[3];

        setCookie('selectedSubMenu', id, 24 * 60);
    });
});

/**
 * @param string name
 * @param string value
 * @param int    life , Life cycle minute
 * @param string path
 */
function setCookie(name, value, life, path) {
    var life = arguments[2] ? arguments[2] : 60;
    var path = arguments[3] ? arguments[3] : '/';

    var exp = new Date();
    exp.setTime(exp.getTime() + life * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";path=" + path + ";expires=" + exp.toGMTString();
}

/**
 * @param  string name
 *
 * @return string
 */
function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

    if (arr = document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}

/**
 * @param  string name
 *
 */
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}