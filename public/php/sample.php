<?php
require_once "jssdk.php";
$jssdk       = new JSSDK("wxa6d691299093f1a4", "7a91a512262ec4a9dea5a507612e48a0");
$signPackage = $jssdk->GetSignPackage();
var_dump($signPackage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>

</body>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    /*
     * 注意：
     * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
     * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
     * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
     *
     * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
     * 邮箱地址：weixin-open@qq.com
     * 邮件主题：【微信JS-SDK反馈】具体问题
     * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
     */
    wx.config({
        debug    : true,
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
                //alert(JSON.stringify(res));
                //alert(JSON.stringify(res.checkResult.getLocation));
                if (res.checkResult.getLocation == false) {
                    alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
                    return;
                }else{
                    wx.chooseImage();
                }
            }
        });
    });
    wx.error(function(res){
        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
        console.log(1111111);
        console.log(res);
        alert("验证失败，请重试！");
        //wx.closeWindow();
    });
</script>
</html>
