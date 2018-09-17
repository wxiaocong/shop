/********************************** add/edit start********************************/
$('.goods-category-button').click(function() {
	art.dialog({
		title:'选择商品分类',
        content: $('.category-template').html(),
        height: '400px',
        width: '600px',
        cancelVal: '关闭',
        cancel: true
	});
});

//good是否上架
$('.good-state input[name="state"]').change(function() {
    var state = $(this).val();
    if (state == 2) {
        $('#tab1').find('.good-sku-state').find('select').each(function() {
            $(this).find('option:eq(0)').attr('selected', false);
            $(this).find('option:eq(1)').attr('selected', true);
            $(this).attr('disabled', true);
        });

        $('input[type="radio"][name="state"]:eq(0)').attr('checked', false);
    } else {
        $('#tab1').find('.good-sku-state').find('select').each(function() {
            $(this).attr('disabled', false);
        });

        $('input[type="radio"][name="state"]:eq(1)').attr('checked', false);
    }
    $(this).attr('checked', true);
});

//sku是否上架
$('#tab1').on('change', '.good-sku-state select', function() {
    if ($('.good-state input[name="state"]:checked').val() == 2) {
        $(this).find('option[value="0"]').attr('selected', false);
        $(this).find('option[value="2"]').attr('selected', true);
    } else {
        if ($(this).val() == 2) {
            $(this).find('option[value="0"]').attr('selected', false);
            $(this).find('option[value="2"]').attr('selected', true);
        } else {
            $(this).find('option[value="2"]').attr('selected', false);
            $(this).find('option[value="0"]').attr('selected', true);
        }
    }
});

$('#tab1').on('click', '.del-good-spec-old-img', function() {
    $(this).parent().parent().remove();
});

//选择模型
$('.model-select').change(function() {
    var operateType = 'add';
    if ($('.good-id').length > 0) {
        operateType = 'edit';
    }
    var modelId = $(this).val();

    //编辑时,判断模型选择是否等于当前商品记录所属模型
    if ($('.current-model-id').length > 0 && modelId == $('.current-model-id').val()) {
        //清空sku,属性
        clearModelDatas();

        //将商品记录关联属性原路塞回
        var goodAttributeHtml = $('.good-attribute-current-template').html();
        $(".good-attribute-table").html(goodAttributeHtml);
        //将商品记录关联spec原路塞回 
        var goodSkuHtml = $('.good-sku-current-template').html();
        $(".good-sku-table").html(goodSkuHtml);
        //初始化上传控件
        initFileUpload();
    } else {
        if (modelId == 0) {
            //清空sku,属性
            clearModelDatas();

            //生成一条新的sku
            var html = '<tr>' + createSkuFields('0', '0', '0', '0', operateType);
            html += createSkuFields('1', '0', '0', '0', operateType) + '</tr>';
            $('#tab1').find(".good-sku-tbody").html(html);
            //初始化上传控件
            initFileUpload();
        } else {
            $.ajax({
                url: '/admin/model/' + modelId + '/findSpec',
                type: 'get',
                dataType: 'json',
                success: function(jsonObject) {
                    if (jsonObject.code == 200) {
                        clearModelDatas();
                       
                        if (jsonObject.datas.length > 0) {
                            var specs=[];//规格
                            var attrs=[];//属性
                            $.each(jsonObject.datas,function (key, val) {
                                if (val.spec == 1) {
                                    specs.push(val);                                        
                                } else{
                                    attrs.push(val);
                                }                                   
                            });
                            createSpecs(specs, operateType);    
                            createAttr(attrs);
                        }
                    } else {
                        tips(jsonObject.messages);
                    }
                },
                error: function(xhr, type) {
                    ajaxResponseError(xhr, type);
                }
            });
        }
    } 
});

if ($('#tab1').find('.fileUpload').length > 0) {
    initFileUpload();
}

//初始化上传控件
function initFileUpload()
{
    $('#tab1').find('.fileUpload').each(function() {
        createFileinput($(this));
        specValueFileuploaded($(this));
    });
}

//监听上传控件上传图片后动作
function specValueFileuploaded(classObj)
{
    classObj.on('fileuploaded', function (event, data, previewId, index){
        if (data.response.code == 200) {
            var obj = $(this).parent().parent();
            var specValueStr = obj.next().val();
            obj.find('.kv-preview-thumb').each(function() {
                var objDiv = $(this).find('.file-upload-indicator');
                var objI = objDiv.find('i');
                var name = 'input[name="goodSku[pic][' + specValueStr + '][]"]';
                var xx = objI.attr('class');
                if ('glyphicon glyphicon-ok-sign text-success' == objI.attr('class') && objDiv.find(name).length == 0) {
                    objDiv.append('<input type="hidden" name="goodSku[pic][' + specValueStr + '][]' + '" value="' + data.response.fileName + '"/>');
                    return false;
                }
            });
        }
    });
}

//初始化上传控件
function createFileinput(className)
{
	className.fileinput({
		headers:{
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    },
	    language: 'zh', //设置语言
	    uploadUrl:'/fileUpload/uploadFile', //上传的地址
	    allowedFileExtensions: ['jpg', 'gif', 'png'],//接收的文件后缀
	    uploadExtraData:{'dataType': 'file'},
	    uploadAsync: true, //默认异步上传
	    showUpload: false, //是否显示上传按钮
	    showRemove: false, //显示移除按钮
	    showPreview: true, //是否显示预览
	    showCaption: false,//是否显示标题
	    browseClass:"btn btn-primary", //按钮样式    
	    dropZoneEnabled: false,//是否显示拖拽区域
	    //minImageWidth: 50, //图片的最小宽度
	    //minImageHeight: 50,//图片的最小高度
	    //maxImageWidth: 1000,//图片的最大宽度
	    //maxImageHeight: 1000,//图片的最大高度
	    //maxFileSize:0,//单位为kb，如果为0表示不限制文件大小
	    //minFileCount: 0,
	    maxFileCount:10, //表示允许同时上传的最大文件个数
	    enctype:'multipart/form-data',
	    validateInitialCount:true,
	    msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
	});
}

//清理模型相关数据
function clearModelDatas() {
    $('#tab1 .good-sku-thead .good-sku-spec').remove();
    $('#tab1 .good-sku-tbody').empty();
    $('#tab1 .good-attribute-tbody').empty();

    //清空关联
    if ($('#oldGoodSku').length > 0) {
        $('#oldGoodSku').find('.good-radio').each(function() {
            $(this).find('.old-good-sku-id').attr('disabled', false);
            $(this).find('.old-good-sku-relation-attr-values').val(0);
        });
    }
}

//生成属性
function createAttr(data)
{
    var html = '';
    $.each(data, function(key, val) {
        html += '<tr><td>' + val.name + '</td>';
        if (val.type == 1) {
            html += '<td>';
            $.each(val.values, function(subKey, subVal) {
                var subK = subKey;
                var subV = subVal;
                html += '<label class="radio-inline">';
                html += '<input type="radio" name="good[attribute][' + val.id + '][]" value="' + subVal + '" />' + subVal + '</label>';
            });
            html += '</td></tr>';
        } else if (val.type == 2) {
            html += '<td>';
            $.each(val.values, function(subKey, subVal) {
                html += '<label class="checkbox-inline">';
                html += '<input type="checkbox" name="good[attribute][' + val.id + '][]" value="' + subVal + '" />' + subVal + '</label>';
            });
            html += '</td></tr>';
        } else if (val.type == 3) {
            html += '<td><select class="form-control" name="good[attribute][' + val.id + '][]">';
            $.each(val.values, function(subKey, subVal) {
                html += '<option value="' + subVal + '">' + subVal + '</option>';
            });
            html += '</select></td></tr>';
        } else if (val.type == 4) {
            html += '<td><input class="form-control" type="text" name="good[attribute][' + val.id + '][]" value="' + (val.values.length > 0 ? val.values[0] : '') + '"/>';
            html += '</td></tr>';
        }       
    });
    
    $('#tab1').find(".good-attribute-tbody").html(html);          
}

//生成sku
function createSpecs(specs, operateType) {
    var len = specs.length;
    var nl = 1;
    var h = new Array(len);
    var rowspans = new Array(len);
    var html = '';
    for (var i = 0; i < len; i++) {
        //组装规格th
        html += '<td class="good-sku-thead-number good-sku-spec">' + specs[i].name + '</a></td>';

        var itemlen = specs[i].values.length;
        if (itemlen <= 0) {
            itemlen = 1
        };
        nl *= itemlen;
        h[i] = new Array(nl);
        for (var j = 0; j < nl; j++) {
            h[i][j] = new Array();
        }
        var l = specs[i].values.length;
        rowspans[i] = 1;
        for (j = i + 1; j < len; j++) {
            rowspans[i] *= specs[j].values.length;
        }
    }
    $('#tab1').find('.good-sku-thead-number').before(html);

    for (var m = 0; m < len; m++) {
        var k = 0,
            b = 0,
            n = 0;
        for (var j = 0; j < nl; j++) {
            var rowspan = rowspans[m];
            h[m][j] = {
                name: specs[m].values[b],
                html: '<td class="good-sku-tbody-spec good-sku-tbody-spec-' + specs[m].id + '" style="width: 5%">' + specs[m].values[b] + '<input name="goodSku[spec][' + specs[m].id + '][]" type="hidden" value="' + specs[m].values[b] + '"/></td>',
                id: specs[m].id,
                replaceName: specs[m].replaceValues[b]
            };
            
            n++;
            if (n == rowspan) {
                b++;
                if (b > specs[m].values.length - 1) {
                    b = 0;
                }
                n = 0;
            }
        }
    }

    html = '';
    for (var i = 0; i < nl; i++) {
        html += '<tr>' + createSkuFields('0', '0', '0', '0', operateType);
        var valueName = '';
        var idsStr = '';
        var valuesStr = '';
        for (var j = 0; j < len; j++) {
            valueName += h[j][i].replaceName;
            if (j == (len-1)) {
                idsStr += h[j][i].id;
                valuesStr += h[j][i].name;
            } else {
                idsStr += h[j][i].id + ',';
                valuesStr += h[j][i].name + ',';
            }
            html += h[j][i].html;
        }
        html += createSkuFields('1', valueName, idsStr, valuesStr, operateType) + '</tr>';
    }
    $('#tab1').find(".good-sku-tbody").html(html);
    //初始化上传控件
    initFileUpload();
}

function createSkuFields(type, valueName, idsStr, valuesStr, operateType)
{
    var html = '';
    if (type == '0') {
        html += '<td>';
        if (operateType == 'edit') {
            html += '<input name="goodSku[id][]" type="hidden" value="0"/>';
        }
        html += '<input class="form-control input-sm" name="goodSku[name][]" type="text" value=""/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[custNo][]" type="text" value=""/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[barCode][]" type="text" value=""/></td>';
    } else {
        html += '<td class="good-sku-tbody-number">';
        html += '<input name="goodSku[specIds][]" type="hidden" value="' + idsStr +'"/>';
        html += '<input name="goodSku[specValues][]" type="hidden" value="' + valuesStr +'"/>';
        html += '<input class="form-control input-sm" name="goodSku[storeNum][]" type="text" value="0" onkeyup="onlyNum(this)"/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[warningNum][]" type="text" value="0" onkeyup="onlyNum(this)"/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[sellPrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[memberPrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[wholesalePrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[costPrice][]" type="text" value="0.00" onkeyup="onlyAmount(this)"/></td>';
        html += '<td><input class="form-control input-sm" name="goodSku[weight][]" type="text" value="0" onkeyup="onlyNum(this)"/></td>';
        if ($('.good-state input[name="state"]:checked').val() == 2) {
            html += '<td class="good-sku-state"><select class="form-control input-sm" name="goodSku[state][]" disabled><option value="0">上架</option><option value="2" selected>下架</option></select></td>';
        } else {
            html += '<td class="good-sku-state"><select class="form-control input-sm" name="goodSku[state][]"><option value="0" selected>上架</option><option value="2">下架</option></select></td>';
        }
        html += '<td>';
        if (operateType == 'edit') {
            html += '<input class="good-sku-spec-ids-str" type="hidden" value="' + valueName + '"/>';
        }
        html += '<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#goodSku' + valueName + '" type="button">图片</button>';
        html += '<div class="modal fade" id="goodSku' + valueName + '" tabindex="-1" role="dialog" aria-labelledby="goodSku' + valueName + 'Label" aria-hidden="true">';
        html += '<div class="modal-dialog">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">';
        html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
        html += '<h4 class="modal-title" id="goodSku' + valueName + 'Label">显示/上传商品Sku图片</h4>';
        html += '</div>';
        html += '<div class="modal-body"><input class="fileUpload file-loading" type="file" name="file" multiple><input type="hidden" value="' + valuesStr + '" /></div>';
        html += '<div class="modal-footer">';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</td>';
        if (operateType == 'edit') {
            html += '<td><button class="btn btn-primary btn-sm good-sku-new-relation-spec" type="button">关联</button></td>';
        }
    }

    return html;
}

$('.edit-good-form').validate({
    submitHandler: function(form) {
        var buttons = $('.good-submit');
        var oldBtnText = buttons.text();
        var id = $('.good-id').val();
        var action = 'put';
        var url = '';
        if (id == '' || id == null || id == 'undefined' || id == 0) {
        	action = 'post';
        	url = '/admin/good/';
        } else {
        	url = '/admin/good/' + id;

            $('.good-attribute-current-template').html('');
            $('.good-sku-current-template').html(''); 
        }
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
        goodCategoryId: {
            required: true
        }
    },
    messages: {
        name: {
            required: "请输入模型名称"
        },
        goodCategoryId: {
            required: "请选择商品分类"
        }
    }
});
/********************************** add/edit end********************************/
/********************************** list start********************************/
//上/下架
$('.good-list-state').change(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
    goodUpdate('/admin/good/' + id + '/updateState', 'post', {});
});
//排序
$('.good-list-sort').change(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	goodUpdate('/admin/good/' + id + '/sort', 'post', {'sort': $(this).val()});
});
//单个删除
$('.good-list-del').click(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	$(this).cnConfirm('确定要删除商品?', function() {
        goodUpdate('/admin/good/' + id, 'delete', {});
    });
});
//批量删除
$('.good-list-batch-del').click(function() {
	if ($("input[name='id[]']:checked").length == 0) {
        $(this).cnAlert('请选择要批量删除的商品', 5);
        return false;
    }    
	$(this).cnConfirm('确定要批量删除商品?', function() {
		var checkedBox = $("input[name='id[]']:checked");
        var ids = new Array();
        checkedBox.each(function() {
            var id = $(this).val();
            ids.push(id);
        });
        goodUpdate('/admin/good/destroyAll', 'post',  {'ids[]': ids});
    });
});

$('.udpate-price').click(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	$.ajax({
        url:  '/admin/good/' + id + '/editGoodPrice',
        type: 'get',
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                art.dialog({
					title:'更新价格',
			        content: jsonObject.datas,
			        ok: function () {
				    	var formObj = $(this).find('form[name="updateGoodPriceForm"]');
			        	goodUpdate($(formObj.selector).attr('action'), 'post', $(formObj.selector).serialize());
				    },
			        cancelVal: '关闭',
			        cancel: true
				});
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
});

$('.udpate-total-num').click(function() {
	var id = $(this).parent().parent().find('input[name="id[]"]').val();
	$.ajax({
        url:  '/admin/good/' + id + '/editGoodNum',
        type: 'get',
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                art.dialog({
					title:'更新库存',
			        content: jsonObject.datas,
			        ok: function () {
			        	var formObj = $(this).find('form[name="updateGoodNumForm"]');
			        	goodUpdate($(formObj.selector).attr('action'), 'post', $(formObj.selector).serialize());
				    },
			        cancelVal: '关闭',
			        cancel: true
				});
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
});

function goodUpdate(url, type, data)
{
	$.ajax({
        url:  url,
        data: data,
        type: type,
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                $(this).cnAlert(jsonObject.messages, 3);
                window.location.href = jsonObject.url;
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
}
/********************************** list end********************************/