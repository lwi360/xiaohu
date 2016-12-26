<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {

    public function add() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        if (!rq('question_id') || !rq('content'))
            return err('question_id and content are required');

        $question = question_ins()->find(rq('question_id'));
        if (!$question)
            return err('question not exists');

        $answer = $this
            ->where(['question_id' => rq('question_id'), 'user_id' => rq('user_id')])
            ->count();

        if ($answer)
            return err('duplicate answers');

        $this->content = rq('content');
        $this->question_id = rq('question_id');
        $this->user_id = rq('user_id');
        return $this->save() ? suc(['id' => $this->id]) : err('dd insert failed');
    }

    public function change() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        /*检查id*/
        if (!rq('id') || !rq('content'))
            return err('id and content are required');

        /*获取指定id Model*/
        $answer = $this->find(rq('id'));

        if (!$answer)
            return err('answer not exists');

        if ($answer->user_id != session('user_id'))
            return err('permission denied');

        $answer->content = rq('content');

        return $answer->save() ? suc(['id' => $answer->id]) : err('dd update failed');
    }

    public function read() {
        if (!rq('id') && !rq('question_id'))
            return err('id or question_id is required');

        if (rq('id')) {
            $answer = $this->find(rq('id'));
            if (!$answer)
                return err('answer not exists');

            return suc(['data' => $answer]);
        }

        if (!question_ins()->find(rq('question_id')))
            return err('question not exists');

        /*查看同一问题下的所有回答*/
        $answer = $this
            ->where('question_id', rq('question_id'))
            ->get()
            ->keyBy('id');

        return suc(['data' => $answer]);
    }

    public function users() {
        return $this
            ->belongsToMany('App\User')
            ->withPivot('vote')
            ->withTimestamps();
    }

    /*投票*/
    public function vote() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        if (!rq('id') || !rq('vote'))
            return err('id and vote are required');

        $answer = $this->find(rq('id'));
        if (!$answer)
            return err('answer not exists');

        /*1为赞同票，2为反对票*/
        $vote = rq('vote') <= 1 ? 1 : 2;

        /*检查此用户是否在相同问题下投过票,如果投过票，清空投票*/
        $answer->users()
            ->newPivotStatement()
            ->where('user_id', session('user_id'))
            ->where('answer_id', rq('id'))
            ->delete();

        /*在连接表中添加数据*/
        $answer->users()
            ->attach(session('user_id'), ['vote' => $vote]);

        return suc();
    }
}
