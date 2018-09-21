/*********************************权限选择start****************************************/
$('.edit-admin-role-form').on('click', '.top-category-checkbox', function () {
    var isCheck = false;
    if (this.checked) {
        isCheck = true;
    }

    var tdObj = $(this).parent().parent().next();
    //选中/取消选中二级权限分类
    checkOperate(tdObj.find('.second-category-checkbox'), isCheck);
    //选中/取消选中权限
    checkOperate(tdObj.find('.right-checkbox'), isCheck);
});

$('.edit-admin-role-form').on('click', '.second-category-checkbox', function () {
    var isCheck = false;
    if (this.checked) {
        isCheck = true;
    }

    //选中/取消选中一级权限分类
    toCategoryCheck($(this).parents('.second-category-and-right-td'), isCheck);

    var rightTr = $(this).parent().parent().parent().next();
    //选中/取消选中权限
    checkOperate(rightTr.find('.right-checkbox'), isCheck);
});

$('.edit-admin-role-form').on('click', '.right-checkbox', function () {
    var isCheck = false;
    if (this.checked) {
        isCheck = true;
    }

    //选中/取消选中二级权限分类
    sendCategoryCheck($(this).parents('.right-td'), isCheck);

    //选中/取消选中一级权限分类
    toCategoryCheck($(this).parents('.second-category-and-right-td'), isCheck);
});

function checkOperate(element, isCheck)
{
    if (element.length > 0) {
        element.each(function () {
            this.checked = isCheck;
        });
    }
}

function sendCategoryCheck(rightTd, isCheck)
{
    if (isCheck) {
        //判断二级权限分类是否选中,已选中跳出
        var categoryInput = rightTd.parent().prev().find('.second-category-checkbox');
        if (categoryInput.is(':checked')) {
            return;
        }

        //判断权限是否都已选中，如都选中，对二级权限分类进行选中
        var count = 0;
        rightTd.find('.right-checkbox').each(function () {
            if (!this.checked) {
                count = 1;
                return false;
            }
        });
        if (count == 0) {
            //选中二级权限分类
            categoryInput.prop('checked', isCheck);
        }
    } else {
        //判断二级权限分类是否选中,已选中跳出
        var categoryInput = rightTd.parent().prev().find('.second-category-checkbox');
        if (categoryInput.is(':checked')) {
            //取消选中二级权限分类
            categoryInput.prop('checked', isCheck);
        }
    }
}

function toCategoryCheck(secondCategoryAndRightTd, isCheck) {
    if (isCheck) {
        //判断一级权限分类是否选中,已选中跳出
        var categoryInput = secondCategoryAndRightTd.prev().find('.top-category-checkbox');
        if (categoryInput.is(':checked')) {
            return;
        }

        //判断二级权限分类是否都已选中，如都选中，对一级权限分类进行选中
        var count = 0;
        secondCategoryAndRightTd.find('.second-category-checkbox').each(function () {
            if (!this.checked) {
                count = 1;
                return false;
            }
        });
        if (count == 0) {
            //选中一级权限分类
            categoryInput.prop('checked', isCheck);
        }
    } else {
        //判断一级权限分类是否选中,未选中跳出
        var categoryInput = secondCategoryAndRightTd.prev().find('.top-category-checkbox');
        if (categoryInput.is(':checked')) {
            //取消选中一级权限分类
            categoryInput.prop('checked', isCheck);
        }
    }
}
/*********************************权限选择end****************************************/

//单个删除
$('.admin-role-list-del').click(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	$(this).cnConfirm('确定要删除角色?', function() {
        adminRoleUpdate(true, '/admin/adminRole/' + id, 'delete', {});
    });
});
//批量删除
$('.admin-role-list-batch-del').click(function() {
	if ($("input[name='id[]']:checked").length == 0) {
        $(this).cnAlert('请选择要批量删除的角色', 5);
        return false;
    }    
	$(this).cnConfirm('确定要批量删除角色?', function() {
		var checkedBox = $("input[name='id[]']:checked");
        var ids = new Array();
        checkedBox.each(function() {
            var id = $(this).val();
            ids.push(id);
        });
        adminRoleUpdate(true, '/admin/adminRole/destroyAll', 'post',  {'ids[]': ids});
    });
});

$('.admin-role-submit').click(function() {
    $('.edit-admin-role-form').submit();
});
$('.edit-admin-role-form').validate({
    submitHandler: function(form) {
        var buttons = $('.admin-role-submit');
        var oldBtnText = buttons.text();
        var id = $('.admin-role-id').val();
        var action = id == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/adminRole/' + id : '/admin/adminRole';
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
            required: '角色名不能为空',
            maxlength: '角色名最多60个字符'
        }
    }
});

function adminRoleUpdate(isJump, url, type, data)
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