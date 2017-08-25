<?php

namespace app\Controllers;

use app\Models\AppModel;
use app\Models\UserModel;
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
       // $this->bindUid($this->fd,1000);
        //$data = $this->client_data->data;

        $data = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('users');
        var_dump($data);
        $this->send($data);
    }

    public function test_push()
    {

        $this->sendToUid(1000,'我是测试');

    }

    public function bind_uid(){

        $this->bindUid($this->fd,10002);

        $this->send('测红宫');
    }

    public function addgroup(){
        $num =yield get_instance()->coroutineCountOnline();
        var_dump($num);
        $this->send($num);
    }

    public function get_group(){

        $res = $this->coroutineGetAllGroups();

        $this->send('dsfsdfas');
    }


}