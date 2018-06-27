var ajaxProcess = 0;
$.ajaxSetup({
    cache: true,
    beforeSend: function () {
        ajaxProcess++;
        if (ajaxProcess == 1) {
            $.loader('show', '<i class="ace-icon fa fa-spinner fa-pulse fa-5x fa-fw orange"></i>');
        }
    },
    complete: function (XMLHttpRequest) {
        if(typeof(XMLHttpRequest.responseJSON) != 'undefined'){
            if(typeof(XMLHttpRequest.responseJSON.loginLose) != 'undefined'){
                layer.msg(XMLHttpRequest.responseJSON.message, {time : 2000}, function(){
                    location.href = XMLHttpRequest.responseJSON.loginLose;
                });
            }
        }
        --ajaxProcess;
        if (ajaxProcess == 0) {
            $.loader('hide');
        }
    }
});

$(':radio').iCheck();

$.validator.addMethod("idcard", function (value, element) {
        return this.optional(element) || checkIdcard(value);
}, "身份证号码不合法");

$.validator.addMethod("requiredIf",function(value,element,param){
    param = param.split(',');
    if(param.length != 2)
    {
        return false;
    }
    var control = param[0];
    var control_value = param[1];
    if($.inArray(control_value,$(control).val()) >= 0)
    {
        if(value == "" || value ==0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    else
    {
        return true;
    }

},"该字段不能为空");


//验证器初始化设置默认值
jQuery
    .validator
    .setDefaults({
        onkeyup:false,
        errorElement: 'div',
        ignore: "",
        errorClass: 'help-block',
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },
        success: function (e,element) {
            try
            {
                $(element).tooltip('destroy');
            }
            finally
            {
                $(element).closest('.form-group').removeClass('has-error');
            }

        },
        errorPlacement: function (error, element) {
                if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                    element
                        .closest('.form-group')
                        .tooltip({
                            title:error.text(),
                            trigger:'hover'
                        });
                } else if (element.is('.select2-hidden-accessible')) {
                    element.parent().tooltip({
                        title:error.text(),
                        trigger:'hover'
                    });
                } else {
                    element.tooltip({
                        title:error.text(),
                        trigger:'hover'
                    });
                }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

$.extend($.validator.messages, {
    required: "该字段不能为空",
    remote: "请修正该字段(该字段值已经被占用)",
    email: "请输入正确格式的电子邮件",
    url: "请输入合法的网址",
    date: "请输入合法的日期",
    dateISO: "请输入合法的日期 (ISO).",
    number: "请输入合法的数字",
    digits: "只能输入整数",
    equalTo: "两次输入的值不相同",
    accept: "请输入拥有合法后缀名的字符串",
    maxlength: $
        .validator
        .format("长度最多是 {0} 的字符"),
    minlength: $
        .validator
        .format("长度最少是 {0} 的字符"),
    rangelength: $
        .validator
        .format("长度介于 {0} 和 {1} 之间的值"),
    range: $
        .validator
        .format("请输入介于 {0} 和 {1} 之间的值"),
    max: $
        .validator
        .format("请输入最大为 {0} 的值"),
    min: $
        .validator
        .format("请输入最小为 {0} 的值")
});

$.extend($.fn.ace_file_input.defaults, {
    style: 'well',
    no_file: '没有选择文件',
    no_icon: 'fa fa-picture-o',
    btn_choose: '单击添加图片',
    btn_change: '单击添加图片',
    icon_remove: 'fa fa-times',
    droppable: false,
    thumbnail: 'large',//large, fit, small
    allowExt: ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
    denyExt: null,
    allowMime:['image/jpg', 'image/jpeg', 'image/png', 'image/gif','application/pdf'],
    denyMime: null,
    maxSize: false,
    previewSize: false,
    previewWidth: false,
    previewHeight: false,
    //callbacks
    before_change: null,
    before_remove: null,
    preview_error: null
});
Date.prototype.Format = function (fmt) {
    var o = {
        "M+": this.getMonth() + 1, //月份
        "D+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(Y+)/i.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};

//检查身份证是否合法
function checkIdcard(num) {
    num = num.toUpperCase();
    if (!(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(num))) {
        return false;
    }
    var len, re;
    len = num.length;
    if (len === 18) {
        re = new RegExp(/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/);
        var arrSplit = num.match(re);
        var dtmBirth = new Date(arrSplit[2] + "/" + arrSplit[3] + "/" + arrSplit[4]);
        var bGoodDay;
        bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
        if (!bGoodDay) {
            return false;
        } else {
            var valnum;
            var arrInt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            var arrCh = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            var nTemp = 0, i;
            for (i = 0; i < 17; i++) {
                nTemp += num.substr(i, 1) * arrInt[i];
            }
            valnum = arrCh[nTemp % 11];
            if (valnum != num.substr(17, 1)) {
                return false;
            }
            return true;
        }
    }
    return false;
}

var $_GET = (function () {
    var url = window
        .document
        .location
        .href
        .toString();
    var u = url.split("?");
    if (typeof(u[1]) == "string") {
        u = u[1].split("&");
        var get = {};
        for (var i in u) {
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();

function unCheckThead(element) {
    if (element.closest('.dataTables_scrollBody').length > 0) {
        var tableThead = element
            .closest('.dataTables_scrollBody')
            .prevAll('.dataTables_scrollHead')
            .find('table');
    } else {
        var tableThead = element;
    }
    tableThead
        .find('thead :checkbox')
        .prop('checked', false);
}

/**
 *设置页面URL参数
 * @param url  传入的地址
 * @param arg  参数的参数名
 * @param arg_val 参数值
 * @returns {string} URL链接
 */
function SetUrl(url, arg, arg_val) {
    var pattern = arg + '=([^&]*)';
    var replaceText = arg + '=' + arg_val;
    var result = "";
    if (url.match(pattern)) {
        var tmp = '/(' + arg + '=)([^&]*)/gi';
        tmp = url.replace(eval(tmp), replaceText);
        result = tmp;
    } else {
        if (url.match('[\?]')) {
            result = url + '&' + replaceText;
        } else {
            result = url + '?' + replaceText;
        }
    }
    return result;
}

/**
 * 在线编辑器
 * @param element  传入DIV(ID),只允许DIV元素
 * @returns {string}
 */
function editor(element) {
    return UE.getEditor(element);
}

$.fn.extend({
    formReset: function () {
        this.resetForm();
        this
            .find('input[type="radio"]')
            .iCheck('update');
        this
            .find('input[type="checkbox"]')
            .iCheck('update');
        this
            .find('input[type="hidden"]')
            .val('');
        this
            .find('select')
            .change();
        this
            .find('.form-group')
            .removeClass('has-error');
        this
            .find('div.help-block')
            .remove();
    }
    });

$.fn.extend({
    fillData: function (form, data) {
        form.find(':input').each(function () {
            switch (this.type) {
                case 'hidden':
                case 'text':
                    $(this).val(data[$(this).attr('name')]);
                    break;
                case 'radio':
                    if ($(this).val() == data[$(this).attr('name')]) {
                        $(this).iCheck('check');
                    } else {
                        $(this).iCheck('uncheck');
                    }
                    break;
                case 'checkbox':
                    if ($.inArray($(this).val(), data[$(this).attr('name')].split(','))) {
                        $(this).iCheck('check');
                    }
                    break;
                case 'select-multiple':
                    $(this).val(data[$(this).attr('name')].split(',')).change();
                    break;
                case 'select-one':
                    /* 列表为空记录预设值，后续再进行处理 */
                    if($(this).find('option').length === 1){
                        $(this).attr('data-val', data[$(this).attr('name')]);
                    }else {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).val(data[$(this).attr('name')]).change();
                        } else {
                            $(this).val(data[$(this).attr('name')]);
                        }
                    }
                    break;
                case 'textarea':
                    $(this).text(data[$(this).attr('name')]);
                    break;
            }
        });
    },
    formLoadData: function (data) {
        /* 普通表单数据填充 */
        this.fillData(this, data);

        /* 列表项处理 */
        for(var i in data){
            if(typeof data[i] === 'object'){
                /* 开始定位列表第一个元素 */
                var current_row = null;
                for(var n in data[i]){
                    var _finded = false;
                    for(var x in data[i][n]){
                        var _firstElement = this.find(':input[name*="' + x + '"]');
                        if(_firstElement.length > 0){
                            current_row = $(_firstElement).closest('tr');
                            _finded = true;
                            break;
                        }
                    }
                    /* 定位完成跳出 */
                    if(_finded){
                        break;
                    }
                }
                /* 开始填充数据 */
                if(current_row != null){
                    for(var n in data[i]){
                        this.fillData(current_row, data[i][n]);
                        $(current_row).clone(true).appendTo($(current_row).closest('tbody'));
                        current_row = $(current_row).closest('tbody').find('tr').eq($(current_row).closest('tbody').find('tr').length - 1);
                    }
                    $(current_row).closest('tbody').find('tr').eq($(current_row).closest('tbody').find('tr').length - 1).remove();
                }
            }
        }
    }
});


$.fn.extend({
        selectUnique: function (child, allOption) {
            var parent = this;
            if (parent.is('select')) {
                parent.off('select2:open focus');
                parent.on('select2:open focus', function () {
                    parent.each(function () {
                            var current = $(this);
                            var tmpOption = allOption.clone();
                            var currentValue = $(this).val();
                            var selectOption = parent
                                .not(current)
                                .find('option[value!=""]:selected');
                            selectOption.each(function () {
                                var selectValue = $(this).val();
                                tmpOption.each(function (index) {
                                    if (selectValue === $(this).val()) {
                                        tmpOption.splice(index, 1);
                                        return false;
                                    }
                                });
                            });
                            current
                                .empty()
                                .append(tmpOption)
                                .find('option[value="' + currentValue + '"]')
                                .attr('selected', 'selected');
                        });
                });
            } else {
                parent.off('select2:open focus', 'select' + child);
                parent.on('select2:open focus', 'select' + child, function () {
                    var allSelect = parent.find('select' + child);
                    allSelect.each(function () {
                        var current = $(this);
                        var tmpOption = allOption.clone();
                        var currentValue = $(this).val();
                        var selectOption = allSelect
                            .not(current)
                            .find('option[value!=""]:selected');
                        selectOption.each(function () {
                            var selectValue = $(this).val();
                            tmpOption.each(function (index) {
                                if (selectValue === $(this).val()) {
                                    tmpOption.splice(index, 1);
                                    return false;
                                }
                            });
                        });
                        current
                            .empty()
                            .append(tmpOption)
                            .find('option[value="' + currentValue + '"]')
                            .attr('selected', 'selected');
                    });
                });
            }
        }
    });

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
