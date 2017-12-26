<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午1:44
 */

namespace app\Models;


use Server\CoreBase\Model;

class SeriesModel extends Model
{
    public function get_series(){
        $array = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('bibi_car_brand_series')
            ->coroutineSend();
        return $array ? $array['result']:array();
    }


    public function syn_series(){

        $values = yield $this->redis_pool->getCoroutine()->sMembers('series');

        if($values){

             return true;

        }else{

            $array = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('bibi_car_brand_series')->coroutineSend();

            if($array){
                $series = $array['result'];

                $items=array();
                foreach($series as $val ){
                    $items[] = $val['brand_series_id'];
                }

                $values = yield $this->redis_pool->getCoroutine()->sAddArray('series',$items);

                if($values){

                    return true;

                }
            }
        }

    }


    public function get_model($model_id){

        $valus = yield $this->redis_pool->getCoroutine()->sMembers('models');


        if($valus){

            $bool  = $this->redis_pool->getCoroutine()->sIsMember('models',$model_id);

            if(!$bool){



            }

        }else{

            $array = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('bibi_car_series_model');

            $items=array();

            foreach($array as $val ){
                   $items[] = $val['model_id'];
            }

            $values = yield $this->redis_pool->getCoroutine()->sAddArray('series',$items);

        }

    }



}