$(function() {
	//表格效果
    $(".AdressTab tbody tr").mousemove(function() {
        if($(this).hasClass("default")) {} else {
            $(this).addClass("TrOn");
        }
    }).mouseout(function() {
        $(this).removeClass("TrOn");
    });

	//添加地址弹窗
	$(".AddForm").click(function() {
		var thisobj = $(this);
		layer.open({
			type: 1,
			area: '585px',
			title: false,
			skin: 'LayPop', //样式类名
			closeBtn: 1, //不显示关闭按钮
			shift: 2,
			btn: ['确定', '取消'],
			yes: function(index, layero) {
				if($('.LayPop b.red').size() < 1) {
					ajaxJson = {
						'Intention': 'SaveAddress',
						'ID': $('.LayPop #ID').val(),
						'Contacts': $('.LayPop #Contacts').val(),
						'ZipCode': $('.LayPop #postcode').val(),
						'Tel': $('.LayPop #phone').val(),
						'IsDefault': $('.LayPop label[name="check"]').hasClass('cb_active'),
						'CitySet': $('.LayPop #CitySet').val(),
						'Address': $('.LayPop #Address').val(),
					};
					if(ajaxJson.CitySet == ''){
						layer.msg('请选择所在地址');
						return
					}else if(ajaxJson.Address == ''){
						layer.msg('请输入详细地址');
						return
					}else if(ajaxJson.ZipCode == ''){
						layer.msg('邮编不能为空');
						return
					}else if(!/^[0-9]\d{5}$/i.test(ajaxJson.ZipCode)){
						layer.msg('邮政编码格式错误');
						return
					}else if(ajaxJson.Contacts == ''){
						layer.msg('请输入收件人姓名');
						return;
					}else if(ajaxJson.Tel == ''){
						layer.msg('手机号码不能为空');
						return
					}else if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxJson.Tel)){
						layer.msg('手机号码格式不正确');
						return
					}
					$.post('/userajax.html', ajaxJson, function(data) {
						layer.msg(data.Message);
						window.location.reload();
					}, 'json');
				} else {
					return false;
				};
			},
			shadeClose: true, //开启遮罩关闭
			content: $(".AdressPop").html(),
			success: function(index, layero) {
				var ID = thisobj.attr('data-id');
				ajaxJson = {
					'Intention': 'GetAddress',
					'ID': ID
				};
				$.post('/userajax.html', ajaxJson, function(data) {
					$('.LayPop #Contacts').val(data.Info.Contacts);
					$('.LayPop #postcode').val(data.Info.ZipCode);
					$('.LayPop #CitySet').val(data.Info.CitySet);
					$('.LayPop #phone').val(data.Info.Tel);
					$('.LayPop #Address').val(data.Info.Address);
					$('.LayPop #ID').val(data.Info.ShippingAddressID);
					var IsDefault = data.Info.IsDefault;
					if(IsDefault == 1) {
						$(".LayPop label[name='check']").addClass("cb_active");
					}
				}, 'json');
				//选择默认地址
				$('.cbt').inputbox();
				$(".AdressBox").on("click", "input[name='City']", function(e) {
					SelCity(this, e);
					$(this).blur(function() {
						$(this).siblings('b.red').remove();
					});
				});
			}
		})
	});

	function ErrTips(Dom, isTrue, Msg) {
		$(Dom).siblings('b.red').remove();
		if(!isTrue) {
			$(Dom).after('<b class="red pl10">*' + Msg + '</b>');
		}
	};

    $(".Delete").click(function() {
        var ID = $(this).attr('data-id');
        ajaxJson = {
            'Intention': 'DelAddress',
            'ID': ID
        };
        layer.confirm('确定删除此地址？', {
            title: false,
            closeBtn: 0,
            btn: ['确定', '取消'],
            yes: function(index, layero) {
                layer.close(index);
                $.post('/userajax.html', ajaxJson, function(data) {
                    layer.msg(data.Message);
                    window.location.reload();
                }, 'json');
            }
        });
    });

	//设置默认地址
	$(".StepAdress").click(function() {
		var ID = $(this).attr('data-id');
		ajaxJson = {
			'Intention': 'SetDefaultAddress',
			'ID': ID
		};
		layer.confirm('确定设置当前为默认地址？', {
			title: false,
			closeBtn: 0,
			btn: ['确定', '取消'],
			yes: function(index, layero) {
				layer.close(index);
				//设置默认地址ajax
				$.post('/userajax.html', ajaxJson, function(data) {
					layer.msg(data.Message);
					window.location.reload();
				}, 'json');
			}
		});
	})
});