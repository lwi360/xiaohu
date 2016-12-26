<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CommonController extends Controller {
    /*时间线api*/
    public function timeline() {
        list($limit, $skip) = paginate(rq('page'), rq('limit'));

        $questions = question_ins()
            ->limit($limit)
            ->skip($skip)
            ->orderBy('created_at', 'desc')
            ->get();

        $answers = answer_ins()
            ->limit($limit)
            ->skip($skip)
            ->orderBy('created_at', 'desc')
            ->get();

        /*合并数据*/
        $data = $questions->merge($answers);

        /*将合并数据按时间排序*/
        $data = $data->sortBy(function ($item) {
            return $item->created_at;
        });

        /*取值*/
        $data = $data->values()->all();

        return $data;
    }
}
