<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午1:44
 */

namespace app\Models;


use Server\CoreBase\Model;

class ModelsModel extends Model
{
    public function insert_models($insert){
        $value = yield $this->mysql_pool->dbQueryBuilder->insert('bibi_car_series_model_copy')
            ->option('HIGH_PRIORITY')
            ->set('series_id', $insert['series_id'])
            ->set('model_id', $insert['model_id'])
            ->set('model_name', $insert['model_name'])
            ->set('model_year', $insert['model_year'])
            ->set('name', $insert['name'])
            ->coroutineSend();
        return $value;
    }

    public function get_models(){

            $array = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('bibi_car_series_model') ->coroutineSend();

            return $array ? $array['result']:array();

    }

    public function syn_models(){

        $values = yield $this->redis_pool->getCoroutine()->sMembers('models');

        if($values){

            return true;

        }else{

            $array = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('bibi_car_series_model')->coroutineSend();

            if($array){
                $series = $array['result'];

                $items=array();
                foreach($series as $val ){
                    $items[] = $val['model_id'];
                }
                $values = yield $this->redis_pool->getCoroutine()->sAddArray('models',$items);

                if($values){
                    return true;
                }
            }
        }

    }


    public function check_model($model_id){

           $res  = $this->syn_models();
           if($res){
               $bool  = yield $this->redis_pool->getCoroutine()->sIsMember('models',$model_id);
               if($bool){
                    return $bool;
               }
           }
    }



}