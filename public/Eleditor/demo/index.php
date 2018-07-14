<?php
require_once "jssdk.php";
$jssdk       = new JSSDK("wxa6d691299093f1a4", "e595b441429a15c3d5526e4accf7cf7f");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- 引入jQuery -->
    <script src="../jquery.min.js"></script>

<!--    <script src="../webuploader.min.js"></script>-->
    <!-- 插件核心 -->
    <script src="../Eleditor.min.js"></script>


    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            -webkit-touch-callout: none;
            background-color: #fff;
            line-height: inherit;
            padding-top: 30px;
        }

        #contentEditor {
            width: 100%;
            min-height: 300px;
            box-sizing: border-box;
            padding: 10px;
            color: #444;
        }

        #contentEditor p {
            letter-spacing: 0.25px;
            font: 16px/25px Tahoma, Verdana, 宋体;
            margin: 20px 0px;
        }

        #contentEditor h4 {
            font-weight: bold;
            line-height: 1.333em;
            margin: 10px 0 20px;
            padding: 25px 0 0;
        }

        #contentEditor img {
            width: 100%;
            height: auto;
            box-sizing: border-box;
        }

        .dempTip {
            border-left: 2px solid #00BCD4;
            padding-left: 5px;
            margin: 10px;
            font-size: 16px;
        }

        code {
            white-space: pre-wrap;
            background: #2D2D2D;
            display: block;
            margin: 10px;
            border-radius: 5px;
            color: #fff;
        }

        .viewTit {
            color: #FF5722;
            position: fixed;
            top: 0;
            left: 0;
            height: 30px;
            line-height: 30px;
            font-size: 14px;
            text-align: center;
            display: block;
            width: 100%;
            background: #FFEB3B;
            box-shadow: 0 0 5px;
        }
    </style>
</head>
<body>

<font class="viewTit">（此编辑器仅适用移动端，chrome请按F12模拟手机设备进行浏览）</font>
<script>
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf('android') >= 0 || ua.indexOf('iphone') >= 0 || ua.indexOf('ipad') >= 0 || $(window).width() <= 500) {
        $('.viewTit').hide();
        $('body').css('padding-top', 0);
    }
</script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>

<div id="contentEditor">
</div>

<script>
    wx.config({
        debug    : false,
        appId    : '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr : '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage'
        ]
    });
    wx.ready(function () {
        // 在这里调用 API
        wx.checkJsApi({
            jsApiList: [
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage'
            ],
            success: function (res) {
                if (res.checkResult.getLocation == false) {
                    alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
                    return;
                }
            }
        });
    });
    wx.error(function(res){
        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
        alert("验证失败，请重试！");
        //wx.closeWindow();
    });
    var contentEditor = new Eleditor({
        el      : '#contentEditor',
        // upload  : {
        //     server       : '/Eleditor/upload.php',
        //     // headers: {
        //     // 	'token': '123123'
        //     // },
        //     compress     : false,
        //     fileSizeLimit: 2
        // },
        uploader: function(){

            /*必须返回一个Promise对象，成功返回url，失败返回错误信息*/
            return new Promise(function(_resolve, _reject){

                /*调用微信接口选取图片*/
                wx.chooseImage({
                    count: 1,
                    sizeType: ['compressed'],
                    success: function (_selected) {
                        if( _selected.localIds.length == 0 ){
                            return;
                        }

                        /*中转到【微信服务器】*/
                        wx.uploadImage({
                            localId: _selected.localIds[0],
                            success: function (_resp) {

                                /*取得图片serverId后传给后端保存处理并返回url*/
                                $.ajax({
                                    url: 'https://www.7qiaoban.cn/Eleditor/upload.php',
                                    type: 'POST',
                                    data: {
                                        /*把serverId传给服务器，服务器取微信换取图片并保存返回url*/
                                        media_id: _resp.serverId
                                    },
                                    cache: false,
                                    success: function(_resu){
                                        console.log(_resu);
                                        if( _resu.status == 0 ){
                                            return _reject(_resu.msg);
                                        }
                                        /*执行resolve并传递url*/
                                        _resolve(_resu.url);
                                    },
                                    error: function(){
                                        _reject('上传失败!');
                                    }
                                });
                            }
                        });

                    }
                });

            });
        },
        /*初始化完成钩子*/
        // mounted : function () {
        //
        //     /*以下是扩展插入视频的演示*/
        //     var _videoUploader = WebUploader.create({
        //         auto     : true,
        //         server   : '服务器地址',
        //         /*按钮类就是[Eleditor-你的自定义按钮id]*/
        //         pick     : $('.Eleditor-insertVideo'),
        //         duplicate: true,
        //         resize   : false,
        //         accept   : {
        //             title     : 'Images',
        //             extensions: 'mp4',
        //             mimeTypes : 'video/mp4'
        //         },
        //         fileVal  : 'video',
        //     });
        //     _videoUploader.on('uploadSuccess', function (_file, _call) {
        //
        //         if (_call.status == 0) {
        //             return window.alert(_call.msg);
        //         }
        //
        //         /*保存状态，以便撤销*/
        //         contentEditor.saveState();
        //         contentEditor.getEditNode().after(`
			// 						<div class='Eleditor-video-area'>
			// 							<video src="${_call.url}" controls="controls"></video>
			// 						</div>
			// 					`);
        //         contentEditor.hideEditorControllerLayer();
        //     });
        // },
        changer : function () {
            console.log('文档修改');
        },
        /*自定义按钮的例子*/
        toolbars: [
            'insertText',
            'editText',
            'insertImage',
            'insertLink',
            'insertHr',
            'delete',
            //自定义一个视频按钮
            {
                id    : 'insertVideo',
                // tag: 'p,img', //指定P标签操作，可不填
                name  : '插入视频',
                handle: function (select, controll) {//回调返回选择的dom对象和控制按钮对象

                    /*因为上传要提前绑定按钮到webuploader，所以这里不做上传逻辑，写在mounted*/

                    /*!!!!!!返回false编辑面板不会关掉*/
                    return false;
                }
            },
            'undo',
            'cancel'
        ]
        //placeHolder: 'placeHolder设置占位符'
    });
</script>

</body>
</html>
