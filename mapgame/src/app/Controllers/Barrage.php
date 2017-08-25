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
class Barrage extends Controller
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

    /**
     * 加入房间
     */
    public function JoinRoom(){
        // $this->bindUid($this->fd,1000);
        $data = $this->client_data->data;
        $value = yield $this->redis_pool->getCoroutine()->sAdd('Room:'.$data->room, $data->uid);
        $value = yield $this->redis_pool->getCoroutine()->set('user_Room_'.$data->uid, $data->room);
        $this->send($data);
    }

    public function QuitRoom(){
        $data = $this->client_data->data;
        $value = yield $this->redis_pool->getCoroutine()->sRem('Room:'.$data->room, $data->uid);

    }


    public function GetRoomMembers(){
        $data = $this->client_data->data;
        $members = yield $this->redis_pool->getCoroutine()->sMembers('Room:'.$data->room);
        $this->send($members);
    }

    /**
     *
     *
     */

    public function SendBarrage()
    {
        $data = $this->client_data->data;
        $members = yield $this->redis_pool->getCoroutine()->sMembers('Room:'.$data->room);
        $response['message']='success';
        $response['info']='成功啦';
        $this->sendToUids( $members,$response);
        //$this->sendToUid(10002,$response);
    }

    public function test_push()
    {

        $this->sendToUid(1000,'我是测试');

    }

    public function bind_uid(){

        $this->bind_uid($this->fd,23);
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