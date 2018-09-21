//是否菜单
$('.right-list-tbody').on('change', '.right-list-show-menu', function() {
    var id = $(this).parent().parent().find('input[name="id[]"]').val();
    rightUpdate(false, '/admin/adminRight/' + id + '/updateShowMenu', 'post', {});
});

//排序
$('.right-list-sort').change(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	rightUpdate(false, '/admin/adminRight/' + id + '/sort', 'post', {'sort': $(this).val()});
});
//单个删除
$('.right-list-del').click(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	$(this).cnConfirm('确定要删除权限?', function() {
        rightUpdate(true, '/admin/adminRight/' + id, 'delete', {});
    });
});
//批量删除
$('.right-list-batch-del').click(function() {
	if ($("input[name='id[]']:checked").length == 0) {
        $(this).cnAlert('请选择要批量删除的权限', 5);
        return false;
    }    
	$(this).cnConfirm('确定要批量删除权限?', function() {
		var checkedBox = $("input[name='id[]']:checked");
        var ids = new Array();
        checkedBox.each(function() {
            var id = $(this).val();
            ids.push(id);
        });
        rightUpdate(true, '/admin/adminRight/destroyAll', 'post',  {'ids[]': ids});
    });
});

$('.right-submit').click(function() {
    $('.edit-right-form').submit();
});
$('.edit-right-form').validate({
    submitHandler: function(form) {
        var buttons = $('.right-submit');
        var oldBtnText = buttons.text();
        var id = $('.right-id').val();
        var action = id == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/adminRight/' + id : '/admin/adminRight';
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
        categoryId: {
            required: true
        },
        name: {
            required: true,
            maxlength: 60
        },
        sortNum: {
            required: true
        },
        showMenu: {
            required: true
        },
        action: {
            required: true
        },
        url: {
            required: true
        }
    },
    messages: {
        categoryId: {
            required: '权限分类ID不能为空'
        },
        name: {
            required: '权限名不能为空',
            maxlength: '权限名最多60个字符'
        },
        sortNum: {
            required: '序列号不能为空'
        },
        showMenu: {
            required: '是否菜单不能为空'
        },
        action: {
            required: 'action不能为空'
        },
        url: {
            required: 'url不能为空'
        }
    }
});

function rightUpdate(isJump, url, type, data)
{
    $.ajax({
        url:  url,
        data: data,
        type: type,
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                $(this).cnAlert(jsonObject.messages, 3);
                if (isJump) {
                    window.location.href = jsonObject.url;
                }
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
}