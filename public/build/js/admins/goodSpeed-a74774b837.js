/**
 * @商品筛选
 */
function searchGoods(config)
{
    art.dialog({
        title:'商品检索',
        content: $('.search-good-template').html(),
        ok: function () {
            var goodSearch = this;
            var brandId = $(".search-good-form select[name='brandId']").val();
            var search = $(".search-good-form input[name='search']").val();
            $.ajax({
                url:  '/admin/speed/findGoods',
                async: false,
                data: {"brandId": brandId, "search": search},
                type: 'get',
                dataType: 'json',
                success: function(jsonObject) {
                    if (jsonObject.code == 200) {
                        var html = '';
                        if (jsonObject.datas.length > 0) {
                            var datas = jsonObject.datas;
                            html += '<div class="container good-list-div" style="min-width:620px;margin-top:10px;height:550px;overflow-y:scroll">';
                            html += '<table class="table table-bordered">';
                            html += '<colgroup>';
                            html += '<col width="30px">';
                            html += '<col width="70px">';
                            html += '<col width="80px">';
                            html += '<col>';
                            html += '<col width="160px">';
                            html += '<col width="90px">';
                            html += '<col width="80px">';
                            html += '</colgroup>';
                            html += '<tbody>';
                            html += '<thead><tr><th></th><th>图片</th><th>sku编号</th><th>商品名称</th><th>品牌</th><th>价格</th><th>库存</th></tr><thead>';

                            for(var i = 0; i < datas.length; i++){
                                var img  = (datas[i]['img'] != null && datas[i]['img'] != '') ? datas[i]['img'] : '';
                                html += '<tr>';
                                html += '<td class="good-radio">';
                                html += '<div class="radio">';
                                html += '<label>';
                                html += "<input type='radio' name='goodIds[]' value='" + datas[i]['id'] + "'/>";
                                html += "<input type='hidden' value='" + datas[i]['number'] + "'/>";
                                html += '</label>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td><img src="' + img + '" width="45px" /></td>';
                                html += '<td>' + datas[i]['id'] + '</td>';
                                html += '<td>' + datas[i]['name'] + '</td>';
                                html += '<td>' + datas[i]['brand'] + '</td>';
                                html += '<td>' + datas[i]['price'] + '</td>';
                                html += '<td>' + datas[i]['number'] + '</td>';
                                html += '</tr>';
                            }
                            html += '</tbody>';
                            html += '</table>';
                            html += '</div>';
                        }

                        $('.speed-goods').prevAll('tr').remove();

                        art.dialog({
                            title:'商品检索',
                            content: html,
                            ok: function () {
                                var goodListObj = $(this).find('.good-list-div');
                                var goodObj = $(goodListObj.selector).find('input[name="goodIds[]"]:checked');
                                //添加选中的商品
                                if(goodObj.length == 0)
                                {
                                    alert('请选择要添加的商品');
                                    return false;
                                }

                                var goodId = goodObj.val();


                                var goodImg = goodObj.parents('.good-radio').next().html();
                                var goodId = goodObj.parents('.good-radio').next().next().html();
                                var goodName = goodObj.parents('.good-radio').next().next().next().html();
                                var goodBrand = goodObj.parents('.good-radio').next().next().next().next().html();
                                var goodPrice = goodObj.parents('.good-radio').next().next().next().next().next().html();
                                var goodNumber = goodObj.parents('.good-radio').next().next().next().next().next().next().html();

                                var html = '';
                                html += '<tr><td>'+goodImg+'<input type="hidden" name="specId" value="' + goodId + '"/></td>';
                                html += '<td>'+goodName+'</td>';
                                html += '<td>'+goodBrand+'</td>';
                                html += '<td>'+goodPrice+'</td>';
                                html += '<td><input text="text" class="form-control" name="price" onkeyup="onlyAmount(this)" placeholder="请填写数字" value="0.00"/></td>';
                                html += '<td>'+goodNumber+'</td>';
                                html += '<td><input text="text" class="form-control" name="totalNum" onkeyup="onlyNum(this)" placeholder="请填写数字" value="0"/></td>';
                                html += '<td><input text="text" class="form-control" name="onceNum" onkeyup="onlyNum(this)" placeholder="请填写数字" value="0"/></td></tr>';
                                $('.speed-goods').before(html);
                            }
                        });
                    } else {
                        showErrorNotice(jsonObject.messages);
                        return false;
                    }
                },
                error: function(xhr, type) {
                    ajaxResponseError(xhr, type);
                    return false;
                }
            });
        }
    });
}

$('.edit-good-speed-form').validate({
    submitHandler: function(form) {
        var buttons = $('.good-speed-submit');
        var oldBtnText = buttons.text();
        var action = $('.good-speed-id').val() == "" ? 'post' : 'put';
        var url = action == 'put' ? '/admin/speed/' + $('.good-speed-id').val() : '/admin/speed';
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
            required: true
        },
        startDate: {
            required: true
        },
        endDate: {
            required: true
        }
    },
    messages: {
        name: {
            required: "请输入限时抢购名称"
        },
        startDate: {
            required: "请选择限时抢购开始时间"
        },
        endDate: {
            required: "请选择限时抢购结束时间"
        }
    }
});

$('.good-speed-list-del').click(function() {
    var id = $(this).next().val();
    $(this).cnConfirm('确定要删除限时抢购?', function() {
        $.ajax({
            url:  '/admin/speed/' + id,
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