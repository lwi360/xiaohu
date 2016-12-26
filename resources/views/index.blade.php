<!doctype html>
<html lang="en" ng-app="xiaohu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>晓乎</title>
    <link rel="stylesheet" href="node_modules/normalize-css/normalize.css">
    <link rel="stylesheet" href="/css/base.css">
    <script src="node_modules/jquery/dist/jquery.js"></script>
    <script src="node_modules/angular/angular.js"></script>
    <script src="node_modules/angular-ui-router/release/angular-ui-router.js"></script>
    <script src="/js/base.js"></script>
</head>
<body>
<div class="navbar clearfix">
    <div class="container">
        <div class="fl">
            <div class="navbar-item brand">晓乎</div>
            <form ng-submit="Question.go_add_question()" ng-controller="QuestionAddController" id="quick_ask">
                <div class="navbar-item">
                    <input type="text" ng-model="Question.new_question.title">
                </div>
                <div class="navbar-item">
                    <button type="submit">提问</button>
                </div>
            </form>
        </div>
        <div class="fr">
            <a class="navbar-item" href="" ui-sref="home">首页</a>
            @if(is_logged_in())
                <a class="navbar-item" ui-sref="login">{{session('username')}}</a>
                <a class="navbar-item" href="{{url('api/user/logout')}}" >登出</a>
            @else
                <a class="navbar-item"  ui-sref="login">登录</a>
                <a class="navbar-item"  ui-sref="signup">注册</a>
            @endif
        </div>
    </div>
</div>
<div class="page">
    <div ui-view=""></div>
</div>
</body>
<script type="text/ng-template" id="home.tpl">
    <div class="home container">
        <h1>最新动态</h1>
        <div class="hr"></div>
        <div class="item-set">
            <div class="item">
                <div class="vote"></div>
                <div class="item-content">
                    <div class="content-act">XXX赞同回答</div>
                    <div class="title"></div>
                    <div class="content-owner"></div>
                    <div class="content-main"></div>
                    <div class="action-set">
                        <div class="comment">评论</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</script>
<script type="text/ng-template" id="login.tpl">
    <div class="login container" ng-controller="LoginController">
        <div class="card">
            <h1>登录</h1>
            [:User.login_data:]
            <form name="login_form" ng-submit="User.login()">
                <div class="input-group">
                    <libal>用户名：</libal>
                    <input name="username" type="text"
                           ng-model="User.login_data.username"
                           ng-minlength="4"
                           ng-maxlength="24"
                           ng-model-options="{debounce:500}"
                           required
                    >
                    <div class="input-error-set" ng-if="login_form.username.$touched">
                        <div ng-if="login_form.username.$error.required">*用户名为必填项</div>
                        <div ng-if="login_form.username.$error.maxlength || login_form.username.$error.minlength">用户名长度需在4至24位之间</div>
                        {{--<div ng-if="User.signup_username_exists">用户名已存在</div>--}}
                    </div>
                </div>
                <div class="input-group">
                    <libal>密码：</libal>
                    <input name="password" type="password"
                           ng-model="User.login_data.password"
                           ng-minlength="6"
                           ng-maxlength="255"
                           required
                    >
                    <div class="input-error-set" ng-if="login_form.password.$touched">
                        <div ng-if="login_form.password.$error.required">*密码为必填项</div>
                        <div ng-if="login_form.password.$error.maxlength ||login_form.password.$error.minlength">密码长度需要在6至255位之间</div>
                        <div ng-if="User.login_failed">用户名或密码有误！</div>
                    </div>
                </div>
                <button type="submit" class="primary" ng-disabled="login_form.$invalid">登录</button>
            </form>
        </div>
    </div>
</script>
<script type="text/ng-template" id="signup.tpl">
    <div class="signup container" ng-controller="SignupController">
        <div class="card">
            <h1>注册</h1>
            {{--[: User.signup_data :]--}}
            <form name="signup_form" ng-submit="User.signup()">
                <div class="input-group">
                    <libal>用户名：</libal>
                    <input name="username" type="text"
                           ng-model="User.signup_data.username"
                           ng-minlength="4"
                           ng-maxlength="24"
                           ng-model-options="{debounce:500}"
                           required
                    >
                    <div class="input-error-set" ng-if="signup_form.username.$touched">
                        <div ng-if="signup_form.username.$error.required">*用户名为必填项</div>
                        <div ng-if="signup_form.username.$error.maxlength || signup_form.username.$error.minlength">用户名长度需在4至24位之间</div>
                        <div ng-if="User.signup_username_exists">用户名已存在</div>
                    </div>
                </div>
                <div class="input-group">
                    <libal>密码：</libal>
                    <input name="password" type="password"
                           ng-model="User.signup_data.password"
                           ng-minlength="6"
                           ng-maxlength="255"
                           required
                    >
                    <div class="input-error-set" ng-if="signup_form.password.$touched">
                        <div ng-if="signup_form.password.$error.required">*密码为必填项</div>
                        <div ng-if="signup_form.password.$error.maxlength ||signup_form.password.$error.minlength">密码长度需要在6至255位之间</div>
                    </div>
                </div>
                <button type="submit" class="primary" ng-disabled="signup_form.$invalid">注册</button>
            </form>
        </div>
    </div>
</script>
<script type="text/ng-template" id="question.add.tpl">
    <div ng-controller="QuestionAddController" class="question-add container">
        <div class="card">
            <form name="question_add_form" ng-submit="Question.add()">
                <div class="input-group">
                    <label>问题标题</label>
                    <input type="text"
                           name="title"
                           ng-minlength="6"
                           ng-model="Question.new_question.title"
                           required
                    >
                </div>
                <div class="input-group">
                    <label>问题描述</label>
                    <textarea type="text" ng-model="Question.new_question.desc"></textarea>
                </div>
                <div class="input-group">
                    <button type="submit" ng-disabled="question_add_form.$invalid">提交</button>
                </div>

            </form>
        </div>
    </div>
</script>
</html>