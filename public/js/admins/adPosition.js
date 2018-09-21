if ($("img[class*='img-upload']").length > 0) {
    // img upload
    $("img[class*='img-upload']").cnFileUpload();
}

$('.ad-position-form').on('click', '.ad-position-del', function() {
    var id = $(this).find('input').val();
    $(this).cnConfirm('确定要删除首页幻灯片?', function() {
        $.ajax({
            url: '/admin/ad/' + id,
            type: 'delete',
            dataType: 'json',
            success: function(jsonObject) {
                if (jsonObject.code == 200) {
                   window.location.href = jsonObject.url;
                } else {
                    showErrorNotice(jsonObject.messages);
                }
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
            }
        }); 
    });
});

$('.ad-position-batch-del').click(function() {
    if ($("input[name='id[]']:checked").length == 0) {
        $(this).cnAlert('请选择要批量删除的首页幻灯片', 5);
        return false;
    }
    $(this).cnConfirm('确定要批量删除首页幻灯片?', function() {
        var checkedBox = $("input[name='id[]']:checked");
        var ids = new Array();
        checkedBox.each(function() {
            var id = $(this).val();
            ids.push(id);
        });
        $.ajax({
            url: '/admin/ad/destroyAll',
            data: {
                'ids[]': ids
            },
            type: 'post',
            dataType: 'json',
            success: function(jsonObject) {
                if (jsonObject.code == 200) {
                   window.location.href = jsonObject.url;
                } else {
                    showErrorNotice(jsonObject.messages);
                }
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
            }
        }); 
    });     
});

$('.edit-ad-position-form').validate({
    submitHandler: function(form) {
        var buttons = $('.ad-position-submit');
        var oldBtnText = buttons.text();
        var action = $('.ad-position-id').val() == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/ad/' + $('.ad-position-id').val() : '/admin/ad';
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
        img: {
            required: true
        },
        url: {
            required: true
        }
    },
    messages: {
        img: {
            required: "请上传图片"
        },
        url: {
            required: "请输入图片对应的url"
        }
    }
});