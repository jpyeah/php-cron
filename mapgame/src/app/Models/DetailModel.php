<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午1:44
 */

namespace app\Models;


use Server\CoreBase\Model;

class DetailModel extends Model
{
    public function insert_detail($insert){

//        isset($insert['Engine_Type']) ? $insert['Engine_Type']  : $insert['Engine_Type'] = 0 ;
//        isset($insert['Engine_Location']) ? $insert['Engine_Location']  : $insert['Engine_Location'] = 0 ;
//        isset($insert['Perf_SeatNum']) ? $insert['Perf_SeatNum']  : $insert['Perf_SeatNum'] = 0 ;
//
//        $value = yield $this->mysql_pool->dbQueryBuilder->insert('bibi_car_model_detail_copy')
//            ->option('HIGH_PRIORITY')
//            ->set('Oil_SupplyType', $insert['Oil_SupplyType'])
//            ->set('Oil_FuelType', $insert['Oil_FuelType'])
//            ->set('Oil_FuelTab', $insert['Oil_FuelTab'])
//            ->set('Oil_FuelCapacity', $insert['Oil_FuelCapacity'])
//            ->set('OutSet_Width', $insert['OutSet_Width'])
//            ->set('OutSet_WheelBase', $insert['OutSet_WheelBase'])
//            ->set('OutSet_MinGapFromEarth', $insert['OutSet_MinGapFromEarth'])
//            ->set('OutSet_Length', $insert['OutSet_Length'])
//            ->set('OutSet_Height', $insert['OutSet_Height'])
//            ->set('Engine_EnvirStandard', $insert['Engine_EnvirStandard'])
//            ->set('Engine_MaxNJ', $insert['Engine_MaxNJ'])
//            ->set('Engine_horsepower', $insert['Engine_horsepower'])
//            ->set('Engine_InhaleType', $insert['Engine_InhaleType'])
//            ->set('Engine_Type', $insert['Engine_Type'])
//            ->set('Engine_Location', $insert['Engine_Location'])
//            ->set('Perf_DriveType', $insert['Perf_DriveType'])
//            ->set('Perf_SeatNum', $insert['Perf_SeatNum'])
//            ->set('Perf_MaxSpeed', $insert['Perf_MaxSpeed'])
//            ->set('Perf_AccelerateTime', $insert['Perf_AccelerateTime'])
//            ->set('Perf_ZongHeYouHao', $insert['Perf_ZongHeYouHao'])
//            ->set('UnderPan_ForwardGearNum', $insert['UnderPan_ForwardGearNum'])
//            ->set('UnderPan_ForwardGearNum_type', $insert['UnderPan_ForwardGearNum_type'])
//            ->set('Engine_ExhaustForFloat', $insert['Engine_ExhaustForFloat'])
//            ->set('Car_RepairPolicy', $insert['Car_RepairPolicy'])
//            ->set('CarReferPrice', $insert['CarReferPrice'])
//            ->set('model_id', $insert['model_id'])
//            ->coroutineSend();
//        return $value;


        $value = yield $this->mysql_pool->dbQueryBuilder->insert('bibi_car_model_detail_copy')
            ->option('HIGH_PRIORITY')
            ->values($insert)
            ->coroutineSend();
        return $value;
    }

    public function syn_details(){

        $values = yield $this->redis_pool->getCoroutine()->sMembers('details');

        if($values){

            return true;

        }else{

            $array = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('bibi_car_model_detail')->coroutineSend();

            if($array){
                $series = $array['result'];
                $items=array();
                foreach($series as $val ){
                    $items[] = $val['model_id'];
                }
                $values = yield $this->redis_pool->getCoroutine()->sAddArray('details',$items);

                if($values){
                    return true;
                }
            }
        }

    }


    public function check_detail($model_id){

        $res  = $this->syn_details();

        if($res){

            $bool  = yield $this->redis_pool->getCoroutine()->sIsMember('details',$model_id);

            if($bool){

                return $bool;
            }

        }

    }



}