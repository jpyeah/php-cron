<?php

namespace app\Controllers;

use app\Models\AppModel;
use Server\CoreBase\Controller;

/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午3:51
 */
class TestController extends Controller
{
    /**
     * @var AppModel
     */
    public $AppModel;

    protected function initialization($controller_name, $method_name)
    {
        parent::initialization($controller_name, $method_name);
        $this->AppModel = $this->loader->model('AppModel', $this);
    }

    public function test(){
       $this->bindUid($this->fd,100, $isKick = true);
        $this->send($this->uid);
    }

    public function test_push()
    {
        $task = $this->loader->task('TestTask');
        $task->test();
        $result = yield $task->coroutineSend();
    }
}