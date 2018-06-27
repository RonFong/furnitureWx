/**
 * Created by seaso on 2017/1/5.
 * iCheck,select2,ace_spinner,datepicker 自定义封装
 */
(function($){
    var _icheck = $.fn.iCheck;
    var _select2  = $.fn.select2;
    var _ace_spinner = $.fn.ace_spinner;
    var _datepicker = $.fn.datepicker;
    var _datatable = $.fn.DataTable;

    //datepicker汉化
    $.fn.datepicker.dates['zh-CN'] = {
        days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"],
        daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
        daysMin:  ["日", "一", "二", "三", "四", "五", "六"],
        months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        monthsShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        today: "今日",
        clear: "清除",
        format: "yyyy-mm-dd",
        titleFormat: "yyyy年mm月",
        weekStart: 1
    };

    //select2汉化
    _select2.amd.define("select2/i18n/zh-CN", [], function () {
        var t=0,n="";
        return {
            errorLoading: function ()
            {
                return "无法载入结果。";
            },
            inputTooLong: function (e)
            {
                 t = e.input.length - e.maximum;
                 n = "请删除" + t + "个字符";
                return n;
            },
            inputTooShort: function (e)
            {
                 t = e.minimum - e.input.length;
                 n = "请再输入至少" + t + "个字符";
                return n;
            },
            loadingMore: function ()
            {
                return "载入更多结果…";
            },
            maximumSelected: function (e)
            {
                t = "最多只能选择" + e.maximum + "个项目";
                return t;
            },
            noResults: function ()
            {
                return "未找到结果";
            },
            searching: function ()
            {
                return "搜索中…";
            }
        }
    });

    //bootstrap样式
    if ( $.fn.dataTable.Api ) {
        $.fn.dataTable.defaults.renderer = 'bootstrap';
        $.fn.dataTable.ext.renderer.pageButton.bootstrap = function ( settings, host, idx, buttons, page, pages ) {
            var api = new $.fn.dataTable.Api( settings );
            var classes = settings.oClasses;
            var lang = settings.oLanguage.oPaginate;
            var btnDisplay, btnClass;

            var attach = function( container, buttons ) {
                var i, ien, node, button;
                var clickHandler = function ( e ) {
                    e.preventDefault();
                    //return if target is disabled
                    if($(e.target).parent().hasClass('disabled')) return false;//ACE
                    if ( e.data.action !== 'ellipsis' ) {
                        api.page( e.data.action ).draw( false );
                    }
                };

                for ( i=0, ien=buttons.length ; i<ien ; i++ ) {
                    button = buttons[i];

                    if ( $.isArray( button ) ) {
                        attach( container, button );
                    }
                    else {
                        btnDisplay = '';
                        btnClass = '';

                        switch ( button ) {
                            case 'ellipsis':
                                btnDisplay = '&hellip;';
                                btnClass = 'disabled';
                                break;

                            case 'first':
                                btnDisplay = lang.sFirst;
                                btnClass = button + (page > 0 ?
                                        '' : ' disabled');
                                break;

                            case 'previous':
                                btnDisplay = lang.sPrevious;
                                btnClass = button + (page > 0 ?
                                        '' : ' disabled');
                                break;

                            case 'next':
                                btnDisplay = lang.sNext;
                                btnClass = button + (page < pages-1 ?
                                        '' : ' disabled');
                                break;

                            case 'last':
                                btnDisplay = lang.sLast;
                                btnClass = button + (page < pages-1 ?
                                        '' : ' disabled');
                                break;

                            default:
                                btnDisplay = button + 1;
                                btnClass = page === button ?
                                    'active' : '';
                                break;
                        }

                        if ( btnDisplay ) {
                            node = $('<li>', {
                                'class': classes.sPageButton+' '+btnClass,
                                'aria-controls': settings.sTableId,
                                'tabindex': settings.iTabIndex,
                                'id': idx === 0 && typeof button === 'string' ?
                                    settings.sTableId +'_'+ button :
                                    null
                            } )
                                .append( $('<a>', {
                                        'href': '#'
                                    } )
                                        .html( btnDisplay )
                                )
                                .appendTo( container );

                            settings.oApi._fnBindAction(
                                node, {action: button}, clickHandler
                            );
                        }
                    }
                }
            };
            attach(
                $(host).empty().html('<ul class="pagination"/>').children('ul'),
                buttons
            );
        }
    }

    $.fn.extend({
        iCheck:function(option){
            if(typeof option === "object" || option === "" || option === undefined)
            {
                var _option = {
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass: 'iradio_flat-blue'
                };
                _option = $.extend(true,_option,option);
                return _icheck.call(this,_option);
            }
            else
            {
                return _icheck.apply(this,arguments);
            }
        }
    });
    $.fn.extend({
        select2:function(option,AjaxUrl,data){
            if(typeof option == "object" || option == "" || option == undefined)
            {
                var _option = {
                    placeholder:'请选择',
                    width:'100%',
                    allowClear:true,
                    dropdownParent:$(this).parent()
                };
                if(AjaxUrl)
                {
                    _option.minimumInputLength = 0;
                    _option.ajax = {
                        type:'post',
                        url:AjaxUrl,  //地址
                        dataType: 'json', //数据类型
                        delay: 250,     //延迟加载时间
                        cache: true,
                        data: function (params) {
                            if( option !== undefined && typeof data == 'function')
                            {
                                return $.extend({'search': params.term,'page': params.page,'row':30},data());
                            }
                            return $.extend({'search': params.term,'page': params.page,'row':30},data);
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * (data.row || 30)) < data.total
                                }
                            };
                        },
                    }
                }
                else
                {
                    _option.minimumResultsForSearch = 10;
                }
                _option = $.extend(true,_option,option);
                return _select2.call(this,_option);
            }
            else
            {
                return _select2.apply(this,arguments);
            }


        }
    });
    $.fn.extend({
        ace_spinner:function(option){
            if(typeof option == "object" || option == "" || option == undefined)
            {
                var _option = {
                    value: 0,
                    min: 0,
                    max: 1000,
                    step: 10,
                    on_sides: true,
                    icon_up: 'ace-icon fa fa-plus bigger-110',
                    icon_down: 'ace-icon fa fa-minus bigger-110',
                    btn_up_class: 'btn-success',
                    btn_down_class: 'btn-danger'
                };
                option = $.extend(_option,option);
                return _ace_spinner.call(this, option);
            }
            else
            {
                return _ace_spinner.apply(this,arguments);
            }
        }
    });
    $.fn.extend({
        datepicker:function(option){
            if(typeof option == "object" || option == "" || option == undefined)
            {
                var _option = {
                    language : 'zh-CN',
                    autoclose : true,
                    disableTouchKeyboard : true,
                    zIndexOffset:2000,
                    clearBtn:true
                };
              this.each(function(){
                  switch ($(this).closest("[data-format]").data('format'))
                  {
                      case 'YYYY':
                          _option.startView = 2;
                          _option.minViewMode = 2;
                          $.extend(_datepicker.dates["zh-CN"],{format:"yyyy"});
                          break;
                      case 'YYYYMM':
                          _option.startView = 1;
                          _option.minViewMode = 1;
                          $.extend(_datepicker.dates["zh-CN"],{format:"yyyymm"});
                          break;
                      case'YYYY-MM':
                          _option.startView = 1;
                          _option.minViewMode = 1;
                          $.extend(_datepicker.dates["zh-CN"],{format:"yyyy-mm"});
                          break;
                      case 'YYYYMMDD':
                          _option.startView = 0;
                          _option.minViewMode = 0;
                          $.extend(_datepicker.dates["zh-CN"],{format:"yyyymmdd"});
                          break;
                      case 'YYYY-MM-DD':
                          _option.startView = 0;
                          _option.minViewMode = 0;
                          $.extend(_datepicker.dates["zh-CN"],{format:"yyyy-mm-dd"});
                          break;
                      default:
                          _option.startView = 0;
                          _option.minViewMode = 0;
                          $.extend(_datepicker.dates["zh-CN"],{format:"yyyy-mm-dd"});
                          break;
                  }
                  _option = $.extend(true,_option,option);
                  _datepicker.call($(this),_option);
              });
            }
            else
            {
                return _datepicker.apply(this,arguments);
            }

        }
    });
    //dataTables
    $.fn.extend({
        DataTable:function(option,sendUrl)
        {
            if(option == undefined)
            {
                option={};
            }
            var _option = {
                language: {
                    "sProcessing": "处理中...",
                    "sLengthMenu": "显示 _MENU_ 项结果",
                    "sZeroRecords": "没有匹配结果",
                    "sInfo": "显示第_START_至_END_项结果，共_TOTAL_项",
                    "sInfoEmpty": "显示第0至0项结果，共0项",
                    "sInfoFiltered": "(由_MAX_项结果过滤)",
                    "sInfoPostFix": "",
                    "sSearch": "搜索:",
                    "sUrl": "",
                    "sEmptyTable": "表中数据为空",
                    "sLoadingRecords": "载入中...",
                    "sInfoThousands": ",",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "上页",
                        "sNext": "下页",
                        "sLast": "末页"
                    },
                    "oAria": {
                        "sSortAscending": ": 以升序排列此列",
                        "sSortDescending": ": 以降序排列此列"
                    },
                    "select": {
                        "rows": {
                            "_": "，已选中 %d 行"
                        }
                    }
                },//中文语言
                dom:"t<'row'<'col-xs-12 col-md-6'i><'col-xs-12 col-md-6'p>>", //DOM显示
                deferRender: true,  //控制表格的延迟渲染，可以提高初始化的速度
                autoWidth: false,   //自动计算宽度
                serverSide : true,  //是否开启服务器模式
                processing: true,    //是否显示正在处理的状态
                pageLength:12,      //每页显示行数
                ordering: true,        //是否排序
                scrollX: true,         //开启横向滚动条
                stateSave: false,  //保存状态 - 在页面重新加载的时候恢复状态（页码等内容)
                info: false,
                ajax:{
                    type:'POST',
                    url:sendUrl,
                    data:function (d) {
                        if(_option.ordering)
                        {
                            d.order = d.columns[d.order[0]['column']]['data'] + ' ' + d.order[0]['dir'];
                        }
                        else
                        {
                            delete d.order;
                        }
                        delete  d.columns;
                        delete  d.search;
                        if(typeof option.search=="object")
                        {
                            for(var item in option.search)
                            {
                                d[item] = option.search[item];
                            }
                        }
                        else if(typeof option.search=="function")
                        {
                            var extendSearch =  option.search();
                            for(var item in extendSearch)
                            {
                                d[item] = extendSearch[item];
                            }
                        }
                    }
                },
                pagingType: "simple_numbers",  //分页显示方式 numbers|simple|simple_numbers|full|full_numbers
                select: {style: 'multi'}//是否选择,multi|single|false
            };
            if(sendUrl == undefined)
            {
                delete _option.ajax;
                _option.serverSide = false;
                _option.processing = false;
            }
            $.each(option.columns,function (i) {
                if(option.columns[i].orderable || option.columns[i].orderable == undefined)
                {
                    _option.order =  [[ i, 'asc' ]];
                    return false;
                }
            });
            if(option.operate || option.operate == undefined)
            {
                _option.columnDefs = [{
                    targets: -1,
                    searchable: false,
                    orderable: false,
                    className:"text-center",
                    render: function(data, type) {
                        var id = "";
                        switch (typeof data)
                        {
                            case 'object':
                                if(data.id)
                                {
                                    id = data.id;
                                }
                                break;
                            default:
                                id = data;
                        }
                        return '<div class="btn-group"> ' +
                                    '<button data-value="'+id+'" class="btn btn-success btn-minier btn-white btn-round" type="button" onclick="edit(this);">' +
                                        '<i class="ace-icon fa fa-pencil bigger-120"></i>修改' +
                                    '</button>' +
                                    '<button data-value="'+id+'" class="btn btn-danger btn-minier btn-white btn-round" type="button" onclick="deleteRow(this);">' +
                                        '<i class="ace-icon fa fa-trash-o bigger-120"></i>删除' +
                                    '</button>'+
                                '</div>'
                    }
                }]
            }
            _option = $.extend(true,_option,option);
            var Table =  _datatable.call(this,_option);
            var element = this;
            var tableThead = undefined;
            if(this.closest('.dataTables_scrollBody').length > 0)
            {
                 tableThead = this.closest('.dataTables_scrollBody').prevAll('.dataTables_scrollHead').find('table');
            }
            else
            {
                 tableThead = element;
            }
            //单击头选择框选中当前页面选择框
            tableThead.on('change','thead > tr > th input:checkbox',function(){
                var th_checked = this.checked;
                element.find('tbody > tr').each(function(){
                    var row = $(this);
                    if(th_checked)
                    {
                        row.find('input:checkbox').prop('checked',true).change();
                    }
                    else
                    {
                        row.find('input:checkbox').prop('checked',false).change();
                    }
                });
            });
            element.on('change','tbody > tr > td input:checkbox', function () {
                var td_checked = this.checked;
                var row = $(this).closest('tr');
                if(td_checked)
                {
                    row.addClass('selected');
                }
                else
                {
                    row.removeClass('selected');
                }
            });
            //窗口大小变化重新渲染
            $(window).on('resize',function () {
                Table.columns.adjust();
            });
            //刷新重新计算宽度
            $(element).on('draw.dt', function () {
                Table.columns.adjust();
                unCheckThead($(this));
            });
            return Table;
        }
    });
    //loader
    jQuery.extend({
        loader: function (action, spinner) {
            var action = action || 'show';
            if (action === 'show') {
                if (this.find('.loader').length == 0) {
                    var loader = $('<div class="loader"></div>').css({
                        'position': 'fixed',
                        'z-index': 10000,
                        'top': '50%',
                        'width': '100%',
                        'margin-top': 0,
                        'text-align': 'center'
                    });
                    var spinner = $(spinner);
                    loader.html(spinner);
                    $('body').append(loader);
                }
            } else if (action === 'hide') {
                $('body').find('.loader').remove();
            }
        }
    });

    jQuery.extend({
        /**
         * 提示框
         * @param title 标题
         * @param content 内容
         * @param status "warning", "error", "success" and "info
         */
        alert: function (title,content,status) {
            swal(title,content,status)
        },
        /**
         * 确认框
         * @param title 标题
         * @param content 内容
         * @param status  "warning", "error", "success" and "info"
         * @param callback 回调函数
         */
        confirm:function (title,content,status,callback) {
            swal({title: title,
                  text: content,
                  type: status,
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "确认",
                  cancelButtonText: "取消",
                  closeOnConfirm: true,
                  closeOnCancel: true },
                  callback)
            }
    });
})(jQuery);