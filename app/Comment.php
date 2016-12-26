<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {
    public function add() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        if (!rq('content'))
            return err('empty content');

        if ((!rq('question_id') && !rq('answer_id')) || rq('question_id') && rq('answer_id'))
            return err('question_id or answer_id is required');

        if (rq('question_id')) {
            $question = question_ins()->find(rq('question_id'));
            if (!$question)
                return err('question not exists');

            $this->question_id = rq('question_id');
        } else {
            $answer = answer_ins()->find(rq('answer_id'));
            if (!$answer)
                return err('answer not exists');

            $this->answer_id = rq('answer_id');
        }

        /*检查是否在回复评论*/
        if (rq('reply_to')) {
            $target = $this->find(rq('reply_to'));
            if (!$target)
                return err('target comment  not exists');

            /*检查是否在评论自己的评论*/
            if ($target->user_id == session('user_id'))
                return err('cannot reply to yourself');

            $this->reply_to = rq('reply_to');
        }
        $this->content = rq('content');
        $this->user_id = session('user_id');

        return $this->save() ? suc(['id' => $this->id]) : err('dd insert failed');
    }

    public function read() {
        if (!rq('question_id') && !rq('answer_id'))
            return err('question_id or answer_id is required');

        if (rq('question_id')) {
            $question = question_ins()->find(rq('question_id'));
            if (!$question)
                return err('question  not exists');

            $data = $this->where('question_id', rq('question_id'));
        } else {
            $answer = answer_ins()->find(rq('answer_id'));
            if (!$answer)
                return err('answer  not exists');

            $data = $this->where('answer_id', rq('answer_id'));
        }

        $data = $data->get()->keyBy('id');
        return suc(['data' => $data]);
    }

    public function remove() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        if (!rq('id'))
            return err('id is required');

        $comment = $this->find(rq('id'));
        if (!$comment)
            return err('comment not exists');

        if ($comment->user_id != session('user_id'))
            return err('permission denied');

        /*先删除所有当前评论下的回复*/
        $this->where('reply_to', rq('id'))->delete();

        return $comment->delete() ? suc(['id' => $this->id]) : err('dd delete failed');
    }
}
