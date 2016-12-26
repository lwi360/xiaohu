<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model {
    /*创建问题*/
    public function add() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        /*检查标题*/
        if (!rq('title'))
            return err('required title');

        $this->title = rq('title');
        $this->user_id = session('user_id');
        if (rq('desc'))
            $this->desc = rq('desc');

        return $this->save() ? suc(['id' => $this->id]) : err('dd insert failed');
    }

    /*问题更新*/
    public function change() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        /*检查id*/
        if (!rq('id'))
            return err('id is required');

        /*获取指定id Model*/
        $question = $this->find(rq('id'));

        if (!$question)
            return err('question not exists');

        if ($question->user_id != session('user_id'))
            return err('permission denied');

        if (rq('title'))
            $question->title = rq('title');
        if (rq('desc'))
            $question->desc = rq('desc');

        return $question->save() ? suc(['id' => $question->id] ): err('dd update failed');
    }

    /*查看问题*/
    public function read() {
        /*检查参数中是否有id*/
        if (rq('id'))
            return suc(['data' => $this->find(rq('id'))]);

//        /*limit条件*/
//        $limit = rq('limit') ?: 15;
//
//        /*skip条件，用于分页*/
//        $skip = (rq('page') ? rq('page') - 1 : 0) * $limit;

        list($limit, $skip) = paginate(rq('page'), rq('limit'));

        /*构建query并返回collection数据*/
        $r = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(['id', 'title', 'desc', 'user_id', 'created_at', 'updated_at'])
            ->keyBy('id');

        return ['status' => 0, 'data' => $r];
    }

    /*删除问题*/
    public function remove() {
        /*检查用户登录*/
        if (!user_ins()->is_logged_in())
            return err('login required');

        if (!rq('id'))
            return err('id is required');

        $question = $this->find(rq('id'));
        if (!$question)
            return err('question not exists');

        if (session('user_id') != $question->user_id)
            return err('permission denied');

        return $question->delete() ? suc(['id' => $question->id]) : err('dd delete failed');
    }


}
