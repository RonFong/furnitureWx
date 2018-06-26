<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"/www/wwwroot/furniture/public/../application/admin/view/login/index.html";i:1529647324;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>数字家具</title>
    <meta name="keywords" content="Study English" />
    <meta name="description" content="Study English" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- basic styles -->

    <link href="__STATIC__/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="__STATIC__/css/font-awesome.min.css" />

    <!--[if IE 7]>
    <link rel="stylesheet" href="__STATIC__/css/font-awesome-ie7.min.css" />
    <![endif]-->

    <!-- page specific plugin styles -->

    <!-- ace styles -->

    <link rel="stylesheet" href="__STATIC__/css/ace.min.css" />
    <link rel="stylesheet" href="__STATIC__/css/ace-rtl.min.css" />

    <!--[if lte IE 8]>
    <link rel="stylesheet" href="__STATIC__/css/ace-ie.min.css" />
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!--[if lt IE 9]>
    <script src="__STATIC__/js/html5shiv.js"></script>
    <script src="__STATIC__/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-layout">
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <i class="icon-leaf green"></i>
                            <span class="red">数字家具</span>
                            <span class="white">Application</span>
                        </h1>
                        <h4 class="blue"><a href="7qiaoban.cn" target="_blank">&copy; 七巧板网络科技</a></h4>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="icon-coffee green"></i>
                                        Please Enter Your Information
                                    </h4>

                                    <div class="space-6"></div>

                                    <form>
                                        <fieldset>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="text" name="account" class="form-control" placeholder="account" />
                                                    <i class="icon-user"></i>
                                                </span>
                                            </label>

                                            <label class="block clearfix next-message">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="password" name="password" class="form-control" placeholder="password" />
                                                    <i class="icon-lock"></i>
                                                </span>
                                            </label>
                                            <div class="space"></div>

                                            <div class="clearfix">
                                                <label class="inline">
                                                    <input type="checkbox" class="ace" />
                                                    <span class="lbl"> Remember Me</span>
                                                </label>

                                                <button type="button" id="submit" class="width-35 pull-right btn btn-sm btn-primary">
                                                    <i class="icon-key"></i>
                                                    Login
                                                </button>
                                            </div>

                                            <div class="space-4"></div>
                                        </fieldset>
                                    </form>

                                    <div class="social-or-login center">
                                        <span class="bigger-110">Or Login Using</span>
                                    </div>

                                    <div class="social-login center">
                                        <a class="btn btn-primary">
                                            <i class="icon-facebook"></i>
                                        </a>

                                        <a class="btn btn-info">
                                            <i class="icon-twitter"></i>
                                        </a>

                                        <a class="btn btn-danger">
                                            <i class="icon-google-plus"></i>
                                        </a>
                                    </div>
                                </div><!-- /widget-main -->

                                <div class="toolbar clearfix">
                                    <div>
                                        <a href="#" class="forgot-password-link">
                                            I forgot my password
                                        </a>
                                    </div>

                                    <div>
                                        <a href="#" class="user-signup-link">
                                            I want to register
                                        </a>
                                    </div>
                                </div>
                            </div><!-- /widget-body -->
                        </div><!-- /login-box -->
                    </div><!-- /position-relative -->
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
</div><!-- /.main-container -->

<script src="__STATIC__/js/jquery-3.2.1.min.js"></script>

<script>
    $('#submit').click(login);
    $(document).keyup(function (event) {
        var keyCode = event.keyCode;
        if (keyCode == 13) {
            login();
        }
    });
    function login () {
        $('#message-text').remove();
        $.ajax({
            url: "<?php echo url('Login/login'); ?>",
            type: 'post',
            data: {
                "account":$("input[name='account']").val(),
                "password":$("input[name='password']").val()
            },
            dataType: 'json',
            success :function (response) {
                if (response.code) {
                    window.location = response.url;
                } else {
                    $('.next-message').after("<p id='message-text' style='color: red;'>" + response.msg + "</p>");
                }
            },
            error: function (err) {
                $('.next-message').after("<p id='message-text' style='color: red;'>内部错误</p>");
            }
        });
    }
</script>


</body>
</html>
