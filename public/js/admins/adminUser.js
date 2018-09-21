//单个删除
$('.admin-user-list-del').click(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	$(this).cnConfirm('确定要删除管理员?', function() {
        adminUserUpdate(true, '/admin/adminUser/' + id, 'delete', {});
    });
});
//批量删除
$('.admin-user-list-batch-del').click(function() {
	if ($("input[name='id[]']:checked").length == 0) {
        $(this).cnAlert('请选择要批量删除的管理员', 5);
        return false;
    }    
	$(this).cnConfirm('确定要批量删除管理员?', function() {
		var checkedBox = $("input[name='id[]']:checked");
        var ids = new Array();
        checkedBox.each(function() {
            var id = $(this).val();
            ids.push(id);
        });
        adminUserUpdate(true, '/admin/adminUser/destroyAll', 'post',  {'ids[]': ids});
    });
});

$('.admin-user-submit').click(function() {
    $('.edit-admin-user-form').submit();
});
$('.edit-admin-user-form').validate({
    submitHandler: function(form) {
        var oldPassword = $("input[name='password']").val();
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('.pubkey').val());
        var encrypted = encrypt.encrypt($("input[name='password']").val());

        $("input[name='password']").val(encrypted);

        var buttons = $('.admin-user-submit');
        var oldBtnText = buttons.text();
        var id = $('.admin-user-id').val();
        var action = id == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/adminUser/' + id : '/admin/adminUser';
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
        error.insertAfter(element);
    },
    // rules
    rules: {
        name: {
            required: true,
            maxlength: 20
        }
    },
    messages: {
        name: {
            required: '用户名不能为空',
            maxlength: '用户名最多20个字符'
        }
    }
});

function adminUserUpdate(isJump, url, type, data)
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