<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('index');
});


function rq($key = null, $default = null) {
    if (!$key) {
        return Request::all();
    } else {
        return Request::get($key, $default);
    }
}

function user_ins() {
    return new App\User;
}

function question_ins() {
    return new App\Question;
}

function answer_ins() {
    return new App\Answer;
}

function comment_ins() {
    return new App\Comment;
}
 function is_logged_in() {
    return session('user_id') ?: false;
}

function paginate($page = 1, $limit = 16) {
    $limit = $limit ?: 16;
    $skip = ($page ? $page - 1 : 0) * $limit;
    return [$limit, $skip];
}

function err($msg = null) {
    return ['status' => 0, 'msg' => $msg];
}

function suc($data_to_merge = null) {
    $data = ['status' => 1, 'data' => []];
    if ($data_to_merge)
        $data['data'] = array_merge($data['data'], $data_to_merge);

    return $data;
}


Route::any('api/user/signup', function () {
    return user_ins()->signup();
});
Route::any('api/user/login', function () {
    return user_ins()->login();
});
Route::any('api/user/read', function () {
    return user_ins()->read();
});
Route::any('api/user/logout', function () {
    return user_ins()->logout();
});
Route::any('api/user/change_password', function () {
    return user_ins()->change_password();
});
Route::any('api/user/reset_password', function () {
    return user_ins()->reset_password();
});
Route::any('api/user/validate_reset_password', function () {
    return user_ins()->validate_reset_password();
});
Route::any('api/user/exist', function () {
    return user_ins()->exist();
});


Route::any('api/question/add', function () {
    return question_ins()->add();
});
Route::any('api/question/change', function () {
    return question_ins()->change();
});
Route::any('api/question/read', function () {
    return question_ins()->read();
});
Route::any('api/question/remove', function () {
    return question_ins()->remove();
});


Route::any('api/answer/add', function () {
    return answer_ins()->add();
});
Route::any('api/answer/change', function () {
    return answer_ins()->change();
});
Route::any('api/answer/read', function () {
    return answer_ins()->read();
});
Route::any('api/answer/vote', function () {
    return answer_ins()->vote();
});


Route::any('api/comment/add', function () {
    return comment_ins()->add();
});
Route::any('api/comment/change', function () {
    return comment_ins()->change();
});
Route::any('api/comment/read', function () {
    return comment_ins()->read();
});
Route::any('api/comment/remove', function () {
    return comment_ins()->remove();
});


Route::any('api/timeline', 'CommonController@timeline');

Route::any('api/test', function () {
    dd(user_ins()->is_logged_in());
});



