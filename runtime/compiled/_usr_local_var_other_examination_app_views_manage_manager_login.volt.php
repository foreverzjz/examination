<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <title>学生在线考试系统</title>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css?v4.0.0">
    <link rel="stylesheet" href="/css/shards.css?v2.0.1">
    <link rel="stylesheet" href="/css/font-awesome.min.css?v4.7.0.2">
    <link rel="stylesheet" href="/css/mc/sign.css">
    <script>
        if (window.top !== window.self) {
            window.top.location = window.location;
        }
    </script>
</head>
<body>
<div class="sign-box">
    <div class="row">
        <div class="col-sm-7">
            <div class="sign-info">
                <div class="logo">
                    <h1 class="text-white">[ XXX ]</h1>
                </div>
                <div class=""></div>
                <h4 class="text-white">欢迎使用 <strong>XXX管理平台</strong></h4>
                <ul class="m-b"></ul>
            </div>
        </div>
        <div class="col-sm-5">
            <form id="frm" method="post" action="<?= $this->url->get('auth') ?>" class="ajaxForm">
                <h4 class="text-white">登录：</h4>
                <p class="m-t-md">XXX管理平台</p>
                <div class="form-group mt-4">
                    <div class="input-group input-group-seamless">
                        <input class="form-control" id="account" name="account" placeholder="登录账号" type="text">
                        <span class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-seamless">
                        <input class="form-control" id="password" name="password" placeholder="登录密码" type="password">
                        <span class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <button id="btnLogin" class="btn btn-primary btn-block">登录</button>
                </div>
            </form>
        </div>
    </div>
    <div class="sign-footer">
        <div class="pull-left">
            &copy; 2018 XXX<sup>&reg;</sup>
        </div>
    </div>
</div>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/bootstrap/js/bootstrap.min.js"></script>
<script src="/js/shards.min.js"></script>
<script src="/js/plugins/SweetAlert/1.2.0.min.js"></script>
<script src="/js/jquery.form4.2.2.min.js"></script>
<script src="/js/mc/main.js"></script>
<script>
    ajaxFormCallback.success = function (data) {
        self.location = '/admin/manager/index';
    }
</script>
</body>
</html>