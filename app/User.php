<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Request;

class User extends Model {
    //
//    public $table ='table_user';
    /**
     *注册api
     */
    public function signup() {
        /**
         * 检查用户名、密码是否为空/是否存在
         */
        $has_username_and_password = $this->has_username_and_password();
        if (!$has_username_and_password) {
            return err('用户名和密码皆不可为空');
        }
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];
        $user_exists = $this->where('username', $username)->exists();

        if ($user_exists) {
            return err('用户名已存在');
        }

        /*密码加密*/
        $ashed_password = bcrypt($password);
        $this->password = $ashed_password;
        $this->phone_captcha='17091645853';
        $this->username = $username;
        if ($this->save()) {
            return suc(['id' => $this->id]);
        } else {
            return err('db insert failed');
        }
    }

    /*登录api*/
    public function login() {
        $has_username_and_password = $this->has_username_and_password();
        if (!$has_username_and_password)
            return err('用户名和密码皆不可为空');

        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];

        $user = $this->where('username', $username)->first();
        if (!$user)
            return err('用户不存在');

        $hashed_password = $user->password;
        if (!Hash::check($password, $hashed_password))
            return err('密码错误');

        /*将用户信息写入session*/
        session()->put('username', $user->username);
        session()->put('user_id', $user->id);
        return suc(['msg' => '登录成功']);
    }

    /*获取用户信息*/
    public function read() {
        if (!rq('id'))
            return err('required id');

        $get = ['id', 'username', 'avatar_url', 'intro'];
        $user = $this->find(rq('id'), $get);

        $data = $user->toArray();

        $answer_count = $user->answers()->count();
//        $question_count = $user->questions()->count();
        $question_count = question_ins()->where('user_id', rq('id'))->count();
        $data['answer_count'] = $answer_count;
        $data['question_count'] = $question_count;

        return $data;
    }

    /*检查用户名和密码是否存在*/
    public function has_username_and_password() {
        $username = rq('username');
        $password = rq('password');
        if (!($username && $password))
            return err('用户名和密码皆不可为空');

        if ($username && $password)
            return [$username, $password];
        else
            return false;
    }

    /*登出api*/
    public function logout() {
        session()->forget('username');
        session()->forget('user_id');
        return suc(['msg' => '登出成功']);
    }

    /*检测用户是否登录*/
    public function is_logged_in() {
        return is_logged_in();
    }

    public function answers() {
        return $this
            ->belongsToMany('App\Answer')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function questions() {
        return $this
            ->belongsToMany('App\Question')
            ->withPivot('vote')
            ->withTimestamps();
    }

    /*修改密码api*/
    public function change_password() {
        if (!$this->is_logged_in())
            return err('login required');

        if (!rq('old_password') || !rq('new_password'))
            return err('old_password and new_password are required');

        $user = $this->find(session('user_id'));

        if (!Hash::check(rq('old_password'), $user->password))
            return err('invalid old_password');

        $user->password = bcrypt(rq('new_password'));
        return $user->save() ? suc() : err('dd update failed');
    }

    /*找回密码*/
    public function reset_password() {
        if ($this->is_robot())
            return err('max frequendy reached');

        if (!rq('phone'))
            return err('phone is required');

        $user = $this->where('phone', rq('phone'))->first();

        if (!$user) {
            return err('invalid phone number');
        }

        $captcha = $this->generate_captcha();


        $user->phone_captcha = $captcha;
        if ($user->save()) {
            $this->send_sms();

            /*记录发送时间(为下次调用做准备)*/
            $this->update_robot_time();
            return suc();
        } else {
            return err('db update failed');
        }

    }

    /*验证找回密码api*/
    public function validate_reset_password() {
        if ($this->is_robot(2))
            return err('max frequendy reached');

        if (!rq('phone') || !rq('phone_captcha'))
            return err('phone and phone_captcha are required');

        /*检查手机号和验证码*/
        $user = $this->where([
            'phone' => rq('phone'),
            'phone_captcha' => rq('phone_captcha')
        ])->first();

        if (!$user)
            return err('invalid phone or invalid phone_captcha');

        $user->password = bcrypt(rq('new_password'));

        $this->update_robot_time();

        return $user->save() ? suc() : err('dd update failed');

    }

    /*生成随机验证码*/

    public function generate_captcha() {
        return rand(1000, 9999);
    }

    public function send_sms() {
        return true;
    }

    public function is_robot($time = 10) {

        if (!session('last_action_time'))
            return false;

        $current_time = time();

        $last_action_time = session('last_action_time');

        $elapsed = $current_time - $last_action_time;

        return !($elapsed > $time);
    }

    public function update_robot_time() {
        session()->set('last_action_time', time());
    }

    public function exist() {
        return suc(['count' => $this->where(rq())->count()]);
    }
}
