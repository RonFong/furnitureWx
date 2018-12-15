/**
 * Created by jiuSuo on 2018/3/1.
 */

/**
 *设置页面URL参数
 * @param url  传入的地址
 * @param arg  参数的参数名
 * @param arg_val 参数值
 * @returns {string} URL链接
 */
function setUrl(url,arg,arg_val)
{
    var pattern=arg+'=([^&]*)';
    var replaceText=arg+'='+arg_val;
    var result = "";
    if(url.match(pattern))
    {
        var tmp='/('+ arg+'=)([^&]*)/gi';
        tmp = url.replace(eval(tmp),replaceText);
        result = tmp;
    }
    else
    {
        if(url.match('[\?]'))
        {
            result =  url+'&'+replaceText;
        }
        else
        {
            result =  url+'?'+replaceText;
        }
    }
    return result;
}

/**
 * 将form中的值转换为键值对
 * @param form
 * @returns {{}}
 */
function getFormJson(form) {
    var res = {};
    var arr = $(form).serializeArray();
    $.each(arr, function () {
        if (res[this.name] !== undefined) {
            if (!res[this.name].push) {
                res[this.name] = [res[this.name]];
            }
            res[this.name].push(this.value || '');
        } else {
            res[this.name] = this.value || '';
        }
    });

    return res;
}

/**
 * 重新加载数据表格
 * @param form 表单id
 * @param curr 当前页码
 */
function reloadTable(form, curr) {
    if (!curr) {curr = 1;}

    var where = {};
    var arr = $(form).serializeArray();
    $.each(arr, function () {
        where[this.name] = this.value || '';
    });
    // history.pushState({}, '', window.location.origin+window.location.pathname);//去除链接参数
    tableIns.reload({
        where: where
        ,page:{
            curr: curr //重新从第 curr 页开始
        }
    });

}

//隐藏/显示侧边栏
$('.nav-display').on('click', function () {
    var sideWidth = $('.layui-side').width();
    $('.tpl-icon-rotate').removeClass('tpl-icon-rotate');
    if (sideWidth === 200) {
        $('.layui-body,.layui-layout-left').animate({left: '0px'});
        $('.layui-side,.layui-layout-admin .layui-logo').animate({width: '0px'});
        $('.nav-display .layui-icon').removeClass('layui-icon-shrink-right').addClass('layui-icon-spread-left');
    } else {
        $('.layui-body,.layui-layout-left').animate({left: '200px'});
        $('.layui-side,.layui-layout-admin .layui-logo').animate({width: '200px'});
        $('.nav-display .layui-icon').removeClass('layui-icon-spread-left').addClass('layui-icon-shrink-right');
    }
});

/*弹窗显示修改密码*/
function changePwd(id, title, url) {
    layui.use('layer', function () {
        $('#pwdFormCommon').find('input[name="user_id"]').val(id);
        var modal = layer.open({
            type: 1
            , title: title
            , btn: ['确定', '取消']
            , content: $('#pwdModalCommon').html()
            , yes: function (index, element) {
                $.post(url, $(element).find('form').serialize(), function (result) {
                    if (result.code) {
                        layer.close(modal);
                        layer.msg(result.msg, {time: 2000});
                    } else {
                        layer.alert(result.msg, {icon: 2, title: '保存失败！'});
                    }
                }, 'json');
            }
        });
    });
}

/*更改排序*/
function changeSort(id, type, url) {
    $.post(url, {id:id, type:type}, function (result) {
        if (result.code) {
            reloadTable('#searchForm');
        } else {
            layer.alert(result.msg, {icon:2});
        }
    }, 'json');
}

//ajax删除
function ajaxDelete(id, url) {
    layer.confirm('数据删除后无法恢复，确定继续吗？', function (index) {
        $.post(url, {id: id}, function (result) {
            layer.close(index);
            if (result.code) {
                reloadTable('#searchForm');
                layer.msg(result.msg);
            } else {
                layer.alert(result.msg, {icon: 2});
            }
        }, 'json');
    });
}

//选中数据，更改状态
function setState(ids, state, field_name, url) {
    if (!field_name) {field_name = 'state';}//状态字段名

    layer.confirm('确定更新状态吗？', function (index) {
        $.post(url, {id:ids, state:state, field_name:field_name}, function (result) {
            if (result.code) {
                reloadTable('#searchForm');//重新加载数据表格
                layer.msg(result.msg, {time: 1000});
            } else {
                layer.alert(result.msg, {icon: 2});
            }
        }, 'json');
    });
}

/*上传图片*/
function uploadImgAjax(element, url, size) {
    if (!size) {size = 2048;}
    if (!url) {url = '/admin/system/uploadimg.html';}
    var lay_load;
    layui.use(['layer', 'upload'], function () {
        var upload = layui.upload;
        upload.render({
            elem: element
            ,url: url
            ,size: size //限制文件大小，单位 KB
            ,exts: "jpg|png|gif|bmp|jpeg"
            ,before: function(obj){
                lay_load = layer.load(2, {time: 20*1000});
            }
            ,done: function(res){
                layer.close(lay_load);
                if(res.code){
                    var control = $(element);
                    $(control).find('.image-text').css('display','none'); //隐藏文字
                    $(control).find('.image-preview').css('display','block'); //显示图片
                    $(control).find('img').attr('src', res.data); //图片链接
                    $(control).find('input[type="hidden"]').val(res.data); //赋值上传
                } else {
                    layer.alert(res.msg);
                }
            }
        });
    });
}

/*删除图片*/
function delImgAjax(element) {
    var dom = $(element).closest('.layui-form-item');
    var field = dom.find('input[type="hidden"]');

    var data = {id:field.data("id"), table_name:field.data("table"), field_name:field.attr("name"), img_url:field.val()};
    layer.confirm('删除后无法恢复，确定继续吗？', function(index){
        $.post('deleteImg', data, function (result) {
            layer.close(index);
            if (result.code) {
                dom.find('.image-text').css('display', 'block');
                dom.find('.image-preview').css('display', 'none');
                dom.find('input[type="hidden"]').val('');
            } else {
                layer.alert(result.msg, {icon:2});
            }
        }, 'json');
    });
}

$('.tools-bottom').on('click', function (e) {
    e.stopPropagation();
});

/*上传大文件*/
function uploadBigFile(element, url, size) {
    if (!size) {size = 512000;}
    if (!url) {url = '/admin/index/uploadImgOss';}
    var lay_load;
    layui.use(['layer', 'upload'], function () {
        var upload = layui.upload;
        upload.render({
            elem: element
            ,url: url
            ,size: size //限制文件大小，单位 KB
            ,accept: 'file' //允许上传的文件类型
            ,before: function(obj){
                lay_load = layer.load(2, {time: 20*1000});
            }
            ,done: function(res){
                layer.close(lay_load);
                if(res.code){
                    var control = $(element).closest('.layui-form-item');
                    control.find('input[type="text"]').val(res.data.url); //赋值上传
                    layer.msg(res.msg);
                } else {
                    layer.alert(res.msg);
                }
            }
        });
    });
}

/*删除大文件*/
function deleteBigFile(element) {
    var dom = $(element).closest('.layui-form-item');
    var field = dom.find('input[type="text"]');

    var data = {id:field.data("id"), table_name:field.data("table"), field_name:field.attr("name"), url:field.val()};
    layer.confirm('删除后无法恢复，确定继续吗？', function(index){
        $.post('/admin/index/deleteOssFile', data, function (result) {
            layer.close(index);
            if (result.code) {
                field.val('');
            } else {
                layer.alert(result.msg, {icon:2});
            }
        }, 'json');
    });
}

(function ($) {
    $.getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]); return null;
    }
})(jQuery);