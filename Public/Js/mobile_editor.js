$(function(){
	//手机端 商品描述
    // 显示隐藏控制面板
    $('div[m-type="mobile_pannel"]').on('click', '.module', function(){
        mobilePannelInit();
        $(this).siblings().removeClass('current').end().addClass('current');
    });
    // 显示图片选择
    $('div[m-type="mobile_add_image_btn"]').click(function(){
    	$("#album-image-list").html('<p class="text-center">加载中...</p>');
    	$('div[m-type="mobile_add_txt_cancel"]').click(); //关闭文字
        $('div[m-type="mobile_image_box"]').show();
        $.getJSON('/index.php?m=Member&c=Goods&a=getAlbumImages', {page : 0}, function(data) {
			if(data){
				$("#album-image-list").html('');
				$.each(data, function(i, n){
					if(n.thumb){
						html = '<div class="col-xs-2" onclick="insert_mobile_img(\''+n.url+'\')"><a href="javascript:void(0);" class="thumbnail"><img src="'+n.thumb+'"></a></div>';
            			$("#album-image-list").append(html);
					}
				})
			}
	    });
    });
    // 插图图片
    insert_mobile_img = function(data){
        _data = new Object;
        _data.type = 'image';
        _data.value = data;
        _rs = mobileDataInsert(_data);
        if (!_rs) {
            return false;
        }
        $('<div class="module m-image"></div>')
        .append('<div class="tools btn-group"><div class="btn btn-default btn-xs" m-type="mobile_tools_up">上移</div><div class="btn btn-default btn-xs" m-type="mobile_tools_down">下移</div><div class="btn btn-default btn-xs" m-type="mobile_tools_del">删除</div></div>')
        .append('<div class="content"><div class="image-div"><img src="' + data + '"></div></div>')
        .append('<div class="cover"></div>').appendTo('div[m-type="mobile_pannel"]');
    }
    // 关闭图片选择
    $('div[m-type="mobile_add_image_cancel"]').click(function(){
    	$('div[m-type="mobile_image_box"]').hide();
    })
    // 显示文字添加
	$('div[m-type="mobile_add_txt_btn"]').click(function(){
		$('div[m-type="mobile_add_image_cancel"]').click();
        $('div[m-type="mobile_txt_box"]').show();
    });
    // 关闭文字添加
    $('div[m-type="mobile_add_txt_cancel"]').click(function(){
        $('textarea[m-type="mobile_txt_content"]').val('');
        $('div[m-type="mobile_txt_box"]').hide();
    });
    $('div[m-type="mobile_add_txt_submit"]').click(function(){
    	var _c = toTxt($('textarea[m-type="mobile_txt_content"]').val().replace(/[\r\n]/g,''));
    	var _cl = _c.length;
        if (_cl == 0 || _cl > 500) {
            return false;
        }
        _data = new Object;
        _data.type = 'text';
        _data.value = _c;
        _rs = mobileDataInsert(_data);
        if (!_rs) {
            return false;
        }
    	$('<div class="module m-text paddingTB-xs"></div>')
    	.append('<div class="tools btn-group"><div m-type="mobile_tools_up" class="btn btn-default btn-xs">上移</div><div m-type="mobile_tools_down" class="btn btn-default btn-xs">下移</div><div m-type="mobile_tools_edit" class="btn btn-default btn-xs">编辑</div><div m-type="mobile_tools_del" class="btn btn-default btn-xs">删除</div></div>')
    	.append('<div class="content"><div class="text-div">' + _c + '</div></div>')
    	.append('<div class="cover"></div>').appendTo('div[m-type="mobile_pannel"]');
    	$('div[m-type="mobile_add_txt_cancel"]').click();
    });
    // 上移
    $('div[m-type="mobile_pannel"]').on('click', '[m-type="mobile_tools_up"]', function(){
    	var _parents = $(this).parents('.module:first');
    	_rs = mobileDataMove(_parents.index(), 0);
        if (!_rs) {
            return false;
        }
        _parents.clone().insertBefore(_parents.prev()).end().end().remove();
        mobilePannelInit();
    });
    // 下移
    $('div[m-type="mobile_pannel"]').on('click', '[m-type="mobile_tools_down"]', function(){
    	var _parents = $(this).parents('.module:first');
    	_rs = mobileDataMove(_parents.index(), 1);
        if (!_rs) {
            return false;
        }
    	_parents.clone().insertAfter(_parents.next()).end().end().remove();
    	mobilePannelInit();
    });
    // 文字编辑
    $('div[m-type="mobile_pannel"]').on('click', '[m-type="mobile_tools_edit"]', function(){
        $('div[m-type="mobile_add_txt_cancel"]').click();
        var _parents = $(this).parents('.module:first');
        var _val = _parents.find('.text-div').html();
        $(this).parents('.module:first').html('')
        .append('<div class="content"></div>').find('.content')
        .append('<div class="mobile-edit-box" m-type="mobile_edit_txt"></div>')
        .find('div[m-type="mobile_edit_txt"]')
        .append('<textarea class="textarea valid" data-old="' + _val + '" m-type="mobile_txt_content">' + _val + '</textarea>')
        .append('<div class="text-center paddingTB-sm btn-group"><div class="col-xs-6"><div class="btn btn-info btn-xs" m-type="mobile_edit_txt_submit">确定</div></div><div class="col-xs-6"><div class="btn btn-danger btn-xs" m-type="mobile_edit_txt_cancel">取消</div></div></div>');
    });
    // 文字编辑提交
    $('div[m-type="mobile_pannel"]').on('click', '[m-type="mobile_edit_txt_submit"]', function(){
        var _parents = $(this).parents('.module:first');
        var _c = toTxt(_parents.find('textarea[m-type="mobile_txt_content"]').val().replace(/[\r\n]/g,''));
        var _cl = _c.length;
        if (_cl == 0 || _cl > 500) {
            return false;
        }
        _data = new Object;
        _data.type = 'text';
        _data.value = _c;
        _rs = mobileDataReplace(_parents.index(), _data);
        if (!_rs) {
            return false;
        }
        _parents.html('').append('<div class="tools btn-group"><div m-type="mobile_tools_up" class="btn btn-default btn-xs">上移</div><div m-type="mobile_tools_down" class="btn btn-default btn-xs">下移</div><div m-type="mobile_tools_edit" class="btn btn-default btn-xs">编辑</div><div m-type="mobile_tools_del" class="btn btn-default btn-xs">删除</div></div>')
            .append('<div class="content"><div class="text-div">' + _c + '</div></div>')
            .append('<div class="cover"></div>');

    });
    // 文字编辑关闭
    $('div[m-type="mobile_pannel"]').on('click', '[m-type="mobile_edit_txt_cancel"]', function(){
        var _parents = $(this).parents('.module:first');
        var _c = _parents.find('textarea[m-type="mobile_txt_content"]').attr('data-old');
        _parents.html('').append('<div class="tools btn-group"><div m-type="mobile_tools_up" class="btn btn-default btn-xs">上移</div><div m-type="mobile_tools_down" class="btn btn-default btn-xs">下移</div><div m-type="mobile_tools_edit" class="btn btn-default btn-xs">编辑</div><div m-type="mobile_tools_del" class="btn btn-default btn-xs">删除</div></div>')
        .append('<div class="content"><div class="text-div">' + _c + '</div></div>')
        .append('<div class="cover"></div>');
    });
    // 删除
    $('div[m-type="mobile_pannel"]').on('click', '[m-type="mobile_tools_del"]', function(){
        var _parents = $(this).parents('.module:first');
        mobileDataRemove(_parents.index());
        _parents.remove();
        mobilePannelInit();
    });
    // 初始化控制面板
    mobilePannelInit = function(){
        $('div[m-type="mobile_pannel"]')
        .find('div[m-type^="mobile_tools_"]').show().end()
        .find('.module')
        .first().find('div[m-type="mobile_tools_up"]').hide().end().end()
        .last().find('div[m-type="mobile_tools_down"]').hide();
    }
    // 插入数据
    mobileDataInsert = function(data){
        _m_data = mobileDataGet();
        _m_data.push(data);
        return mobileDataSet(_m_data);
    }
    // 数据移动
    // type 0上移  1下移
    mobileDataMove = function(index, type) {
        _m_data = mobileDataGet();
        _data = _m_data.splice(index, 1);
        if (type) {
            index += 1;
        } else {
            index -= 1;
        }
        _m_data.splice(index, 0, _data[0]);
        return mobileDataSet(_m_data);
    }
    // 数据移除
    mobileDataRemove = function(index){
        _m_data = mobileDataGet();
        _m_data.splice(index, 1);     // 删除数据
        return mobileDataSet(_m_data);
    }
    // 替换数据
    mobileDataReplace = function(index, data){
        _m_data = mobileDataGet();
        _m_data.splice(index, 1, data);
        return mobileDataSet(_m_data);
    }
    // 获取数据
    mobileDataGet = function(){
        _m_body = $('textarea[name="info[m_content]"]').val();
        if (_m_body == '' || _m_body == 'false') {
            var _m_data = new Array;
        } else {
            eval('var _m_data = ' + _m_body);
        }
        return _m_data;
    }
    // 设置数据
    mobileDataSet = function(data){
        var _i_c = 0;
        var _i_c_m = 20;
        var _t_c = 0;
        var _t_c_m = 5000;
        var _sign = true;
        $.each(data, function(i, n){
            if (n.type == 'image') {
                _i_c += 1;
                if (_i_c > _i_c_m) {
                    alert('只能选择'+_i_c_m+'张图片');
                    _sign = false;
                    return false;
                }
            } else if (n.type == 'text') {
                _t_c += n.value.length;
                if (_t_c > _t_c_m) {
                    alert('只能输入'+_t_c_m+'个字符');
                    _sign = false;
                    return false;
                }
            }
        });
        if (!_sign) {
            return false;
        }
        $('span[nctype="img_count_tip"]').html('还可以选择图片<em>' + (_i_c_m - _i_c) + '</em>张');
        $('span[nctype="txt_count_tip"]').html('还可以输入<em>' + (_t_c_m - _t_c) + '</em>字');
        if(data.length == 0){
            $('textarea[name="info[m_content]"]').val('');
            return true;
        }else{
            _data = JSON.stringify(data);
            $('textarea[name="info[m_content]"]').val(_data);
            return true;
        }

    }

    // 转码
    toTxt = function(str) {
        var RexStr = /\<|\>|\"|\'|\&|\\/g
        str = str.replace(RexStr, function(MatchStr) {
            switch (MatchStr) {
            case "<":
                return "";
                break;
            case ">":
                return "";
                break;
            case "\"":
                return "";
                break;
            case "'":
                return "";
                break;
            case "&":
                return "";
                break;
            case "\\":
                return "";
                break;
            default:
                break;
            }
        })
        return str;
    }
})