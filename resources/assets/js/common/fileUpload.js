// 文件上传插件
(function($) {
	// 可以进行压缩的图片格式
	var imgCompressSuffix = ["jpg", "jpeg", "png", "bmp"];

	$.fn.cnFileUpload = function(options) {
		var options = $.extend({
			"fileTypes": imgCompressSuffix, // 文件类型，默认上传图片
			"maxSize": 5120, // KB (5MB)，文件大小
			"url": "/fileUpload/uploadLocalFile", // 文件上传到服务器路径
			// 这是压缩图片的参数，只要上传的是超额的图片，就一定会压缩
			"imgMaxWidth": 1920, // 图片最大的宽度，如果上传的是图片的话，会因超过"maxSize"而进行压缩的宽度
			"imgQuality": 0.9 // 压缩图片的质量，从1.0 ~ 0.1
		}, options);

		return this.each(function(index) {
			var preview = $(this);
			if (preview.is("img")) {
				imgStyle(preview);
				// 绑定选中上传文件后的触发事件
				preview.parent().find("input[type='file']").on("change", function() {
					if (!window.applicationCache) {
						showErrorNotice(["无法启动本系统文件上传功能,请使用支持Html5的浏览器!"]);
						return;
					}

					var file = this.files[0];
					if (file != undefined) {
						var fileInfo = extractFileInfo(file);
						var fileTypes = options.fileTypes;
						if (!checkFileType(fileInfo["fileSuffix"], fileTypes)) {
							showErrorNotice(["只能上传【" + fileTypes.toString() + "】格式文件！"]);
						} else {
							// 第二次检验文件格式是不是图片
							if (!checkFileType(fileInfo["fileSuffix"], imgCompressSuffix)) {
								if (fileInfo["fileSize"] > options.maxSize) {
									showErrorNotice(["上传的文件大小超过限定【" + options.maxSize / 1024 + "MB】"]);
									return;
								}
								// 封装好数据，准备上传
								var formData = new FormData();
								formData.append("dataType", "file");
								formData.append("file", file);
								showUploadStatus(preview);
								fileUpload(formData, preview, options.url, fileInfo);
								drawFileThumbnail(preview, fileInfo["fileName"], fileInfo["fileSuffix"]);
							} else {
								compressImageAndUpload(
									fileInfo,
									options.maxSize,
									options.imgMaxWidth,
									options.imgQuality,
									preview,
									options.url
								);
							}
						}
					}
				});
			} else {
				preview.cnAlert("上传插件绑定元素必须是【img】");
			}
		});
	}

	// 清空上传插件内容
	$.fn.cnClearFileInput = function(){
		return this.each(function(i){
			var preview = $(this);
			if (preview.is("img")) {
				defaultStyle(preview);
			} else {
				preview.cnAlert("上传插件绑定元素必须是【img】");
			}
		});
	}

	// 更改预览框的样式
	var imgStyle = function(preview) {
		var title = preview.attr("title");
		title = title == undefined ? "" : title;
		var name = preview.attr("name");
		var width = preview.css("width");
		var imgWidth = parseInt(width.replace("px", ""));
		var imgHeight = parseInt(width.replace("px", ""));
		var maxSize = (imgWidth >= imgHeight ? imgWidth : imgHeight) + "px";

		var imgDiv = $("<div></div>");
		var clickScope = $("<div class='fileinput-button'></div>");
		var inputFile = $("<input type='file'>");
		var inputHidden = $("<input name='" + name + "' type='hidden'/>");
		var altDiv = $("<div class='text-center'>" + title + "</div>");

		preview.before(imgDiv);
		imgDiv.append(clickScope);
		imgDiv.append(altDiv);
		clickScope.append(preview);
		clickScope.append(inputFile);
		clickScope.append(inputHidden);

		if ($.trim(preview.attr("src")) == "") {
			preview.attr("src", defaultTthumbnail);
		} else {
			inputHidden.val("0");
			var rb = removeBtn.clone();
			rb.on(removeBtnEvent);
			preview.before(rb);
		}

		preview.addClass("thumbnail");
		preview.css("margin", "0px");
		imgDiv.css("width", width);
		altDiv.css({
			"font-size": "12px",
			"color": "#848484"
		});
		inputFile.css("font-size", maxSize);
	}

	// 检验上传文件格式是否符合要求
	var checkFileType = function(fileSuffix, fileTypes) {
		for (var i = 0; i < fileTypes.length; i++) {
			if (fileTypes[i].toLowerCase() == fileSuffix.toLowerCase()) {
				return true;
			} else if (i == fileTypes.length - 1) {
				return false;
			}
		}
	}

	// 对图片进行压缩
	var compressImageAndUpload = function(
		fileInfo,
		maxSize,
		imgMaxWidth,
		imgQuality,
		preview,
		url
	) {
		showUploadStatus(preview);
		var URL = window.URL || window.webkitURL;
		var img = new Image();
		img.onload = function() {
			if (fileInfo["fileSize"] > maxSize) {

				// 准备画布大小
				var width = img.width,
					height = img.height;
				scale = width / height;
				width = parseInt(imgMaxWidth);
				height = parseInt(width / scale);

				// 生成画布
				var canvas = document.createElement("canvas");
				var ctx = canvas.getContext('2d');
				canvas.width = width;
				canvas.height = height;

				// 给画布上白底色,防止压缩PNG到JPG时透明部分变黑底色
				ctx.fillStyle = "#fff";
				ctx.fillRect(0, 0, width, height);

				// 把图片绘制到画布上
				ctx.drawImage(img, 0, 0, width, height);
				var imgBase64 = canvas.toDataURL('image/jpeg', imgQuality);

				// 文件图片添加到预览框内
				preview.attr("src", imgBase64);

				// 封装好数据，准备上传
				var formatLen = "data:image/jpeg;base64,".length;
				var formData = new FormData();
				formData.append("dataType", "base64");
				formData.append("file", imgBase64.substr(formatLen));
				fileUpload(formData, preview, url, fileInfo);

				// 释放内存中本地的图片对象
				URL.revokeObjectURL(imgUrl);
			} else {
				// 文件图片添加到预览框内
				preview.attr("src", imgUrl);

				// 封装好数据，准备上传
				var formData = new FormData();
				formData.append("dataType", "file");
				formData.append("file", fileInfo["file"]);

				fileUpload(formData, preview, url, fileInfo);
			}
		}

		// 从内存生成一个本地图片的URL对象
		var imgUrl = URL.createObjectURL(fileInfo["file"]);
		img.src = imgUrl;
	}
	// 上传文件
	var fileUpload = function(
		formData,
		preview,
		url,
		fileInfo
	) {
		$.ajax({
			url: url,
			type: "POST",
			data: formData,
			dataType: "json",
			processData: false, // 告诉jQuery不要去处理发送的数据
			contentType: false, // 告诉jQuery不要去设置Content-Type请求头
			success: function(responseJSON) {
				if (responseJSON.code != 200) {
					showErrorNotice(responseJSON.messages);
					defaultStyle(preview);
				} else {
					var imgNameInput = preview.next().next();
					imgNameInput.val(responseJSON.fileName);
					var rb = removeBtn.clone();
					rb.on(removeBtnEvent);
					preview.before(rb);
					closeStatus(preview);
				}
			},
			error: function(xhr, type) {
				ajaxResponseError(xhr, type);
				defaultStyle(preview);
			}
		});
	}

	// 画出文件缩略图
	var drawFileThumbnail = function(preview, fileName, fileSuffix) {
		var canvas = document.createElement("canvas");
		canvas.width = preview.css("width").replace("px", "");
		canvas.height = preview.css("height").replace("px", "");

		var ctx = canvas.getContext('2d');

		// 背景色
		ctx.fillStyle = "#eee";
		ctx.fillRect(0, 0, canvas.width, canvas.height);

		// 后缀名居中
		ctx.fillStyle = "#000";
		ctx.font = '36px Helvetica';
		ctx.textBaseline = 'middle';
		ctx.textAlign = 'center';
		ctx.fillText(fileSuffix, canvas.width / 2, canvas.height * 0.4);

		// 文件名超长处理
		ctx.fillStyle = "#fff";
		ctx.font = '12px Helvetica';
		var textWidth = ctx.measureText(fileName).width;
		if (textWidth > canvas.width * 0.7) {
			for (var i = 1; i < fileName.length; i++) {
				textWidth = ctx.measureText(fileName.substring(0, fileName.length - i)).width;
				if (textWidth <= canvas.width * 0.7) {
					fileName = fileName.substring(0, fileName.length - i) + "...";
					break;
				}
			}
		}

		// 文件名的圆角黑色背景
		ctx.beginPath();
		var x = canvas.width * 0.3 / 2 + (canvas.width * 0.7 - textWidth) / 2;
		var y = canvas.height * 0.75;
		ctx.moveTo(x, y);
		x += textWidth;
		ctx.lineTo(x, y);
		ctx.lineWidth = 18;
		ctx.strokeStyle = '#848484';
		ctx.lineCap = 'round';
		ctx.stroke();

		ctx.fillText(fileName, canvas.width / 2, canvas.height * 0.75);

		var imgBase64 = canvas.toDataURL('image/jpeg', 0.9);
		preview.attr("src", imgBase64);
	}

	// 提取上传文件的信息
	var extractFileInfo = function(file) {
		var lastIndex = file.name.lastIndexOf(".");
		var fileSuffix = file.name.substring(++lastIndex);
		var fileName = file.name.substring(0, --lastIndex);
		var fileSize = Math.round(file.size / 1024 * 100) / 100; // KB
		return {
			"file": file,
			"fileSuffix": fileSuffix,
			"fileName": fileName,
			"fileSize": fileSize
		};
	}

	// 预览框显示上传状态
	var showUploadStatus = function(preview) {
		preview.closest(".fileinput-button").find(".glyphicon").remove();
		var coverClone = coverDiv.clone();
		preview.before(coverClone);
		coverClone.after(progressDiv.clone());
	}

	// 预览框关闭遮流罩与进度条
	var closeStatus = function(preview) {
		preview.closest(".fileinput-button").find(".cover,.progress-div").remove();
	}

	// 预览框变回初始状态
	var defaultStyle = function(preview) {
		closeStatus(preview);
		preview.closest(".fileinput-button").find(".glyphicon").remove();
		var imgNameInput = preview.next().next();
		preview.attr("src", defaultTthumbnail);
		imgNameInput.val("");
		// reset file input
		var form = $("<form></form>");
		var inputFile = preview.next();
		inputFile.before(form);
		form.append(inputFile);
		form[0].reset();
		form.before(inputFile);
		form.remove();
	}

	var removeBtnEvent = {
		mouseover: function() {
			$(this).css("color", "#FF0000");
		},
		mouseout: function() {
			$(this).css("color", "#E80D52");
		},
		mousedown: function() {
			$(this).css("color", "#B11848");
		},
		mouseup: function() {
			$(this).css("color", "#FF0000");
		},
		click: function() {
			var removeBtn = $(this);
			removeBtn.cnConfirm("确定移除该文件?", function() {
				defaultStyle(removeBtn.next());
			});
		}
	};

	// 遮流罩
	var coverDiv = $("<div class='cover'></div>").css({
		"position": "absolute",
		"margin": "1px",
		"background-color": "#000000",
		"height": "100%",
		"filter": "alpha(opacity=30)",
		"opacity": "0.3",
		"overflow": "hidden",
		"width": "100%",
		"z-index": "998"
	});

	// 进度条
	var progressDiv = $("<div class='progress-div'></div>").append(
		$("<div class='progress'></div>").append(
			$("<div class='progress-bar progress-bar-striped progress-bar-success active'>上传中...</div>")
			.attr({
				"role": "progressbar",
				"aria-valuenow": "100",
				"aria-valuemin": "0",
				"aria-valuemax": "100"
			})
			.css({
				"width": "100%",
				"z-index": "999"
			})
		).css("margin", "0")
	).css({
		"position": "absolute",
		"bottom": "5px",
		"padding": "0 5px 0 5px",
		"width": "100%",
		"z-index": "999"
	});

	// 清空按钮
	var removeBtn = $("<div class='glyphicon glyphicon-remove'></div>")
		.css({
			"position": "absolute",
			"right": "5px",
			"top": "5px",
			"font-size": "20px",
			"color": "#E80D52",
			"z-index": "997",
			"cursor": "pointer"
		});

	var defaultTthumbnail = "data:image/png;base64,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BCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgDQhBCBNCAFIE0IA0oQQgLDfy1+IJP5+ZOS5dAAAAABJRU5ErkJggg==";
})(jQuery);