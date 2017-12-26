<?php
namespace app\Tasks;

use Server\CoreBase\Task;

/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午1:06
 */
class CarTask extends Task
{
    public $SeriesModel;

    public $ModelsModel;

    public $DetailModel;

    public function __construct()
    {
        parent::__construct();

        $this->SeriesModel  =  $this->loader->model('SeriesModel',$this);
        $this->ModelsModel  =  $this->loader->model('ModelsModel',$this);
        $this->DetailModel  =  $this->loader->model('DetailModel',$this);
    }

    public function series(){

        $res = yield $this->SeriesModel->syn_series();
        if($res){
            //随机抽取一个系列查找是否更新
            $key = yield $this->redis_pool->getCoroutine()->sRandMember('series');
            //执行之后从缓存中去除
            $res = yield $this->redis_pool->getCoroutine()->sRem('series',$key);
            yield $this->setmodel($key);
            //异步执行更新查找model
        }
    }


     function setmodel($series_id){

        $url="http://carapi.ycapp.yiche.com/car/GetCarListV61?csid=".$series_id."&cityId=502";

        $html=file_get_contents($url);

        $data = json_decode($html,true);

        if(isset($data['data'])){

                $data=$data['data'];

                foreach($data as $key =>$value){
                    if(!empty($value["CarGroup"]["CarList"])){
                        $Name=$value["CarGroup"]["Name"];
                        foreach($value["CarGroup"]["CarList"]  as $key => $value){

        //                        $log = date('Y-m-d h:i:s',time())."--- Model ADD : ".$value['CarId'];
        //                        $this->log->info($log);
                            $info['model_id']=$value['CarId'];
                            $info['series_id']=$series_id;
                            $info['model_name']=$value["Year"]." ".$value["Name"];
                            $info['model_year']=$value["Year"];
                            $info['name']=$Name;
                            $res = yield $this->ModelsModel->check_model($value['CarId']);
                            if(!$res){
                                $int_res =  yield $this->ModelsModel->insert_models($info);
                                if($int_res){
                                    $res = yield $this->redis_pool->getCoroutine()->sAdd('models',$value['CarId']);

                                    print_r("Model-insert : ".$value['CarId']);
                                    print_r("</br>");

                                    yield  $this->setmodeldetail($value['CarId']);
        //                                $log = date('Y-m-d h:i:s',time())."Model ADD : ".$value['CarId'];
        //                                yield $this->log->error($log);
                                }
                            }
                        }
                    }
                }
        }


    }

     function setmodeldetail($model_id){

        $res = yield $this->DetailModel->check_detail($model_id);

        if(!$res){

            $url="http://carapi.ycapp.yiche.com/Car/GetCarStylePropertys?carIds=".$model_id;
            $html=file_get_contents($url);
            $data=json_decode($html,true);

            $arr=array();
            $arr["model_id"]=$model_id;
            if(isset($data["data"][0]["CarReferPrice"])){
                $arr["CarReferPrice"]=$data["data"][0]["CarReferPrice"];
            }

            if(isset($data["data"][0]["Car_RepairPolicy"])){
                $arr["Car_RepairPolicy"]=$data["data"][0]["Car_RepairPolicy"];
            }

            if(isset($data["data"][0]["Engine_ExhaustForFloat"])){
                $arr["Engine_ExhaustForFloat"]=$data["data"][0]["Engine_ExhaustForFloat"];
            }

            if(isset($data["data"][0]["UnderPan_ForwardGearNum"])){
                $b = '手动';
                if(strpos($data["data"][0]["UnderPan_ForwardGearNum"], $b) !== false ){
                    //包含
                    $arr["UnderPan_ForwardGearNum_type"]=1;
                }else{
                    $arr["UnderPan_ForwardGearNum_type"]=2;
                }
                $arr["UnderPan_ForwardGearNum"]=$data["data"][0]["UnderPan_ForwardGearNum"];
            }

            if(isset($data["data"][0]["Perf_ZongHeYouHao"])){
                $arr["Perf_ZongHeYouHao"]=$data["data"][0]["Perf_ZongHeYouHao"];
            }

            if(isset($data["data"][0]["Perf_AccelerateTime"])){
                $arr["Perf_AccelerateTime"]=$data["data"][0]["Perf_AccelerateTime"];
            }

            if(isset($data["data"][0]["Perf_MaxSpeed"])){
                $arr["Perf_MaxSpeed"]=$data["data"][0]["Perf_MaxSpeed"];
            }

            if(isset($data["data"][0]["Perf_SeatNum"])){
                $arr["Perf_SeatNum"]=$data["data"][0]["Perf_SeatNum"][0];
            }

            if(isset($data["data"][0]["Perf_DriveType"])){
                $arr["Perf_DriveType"]=$data["data"][0]["Perf_DriveType"];
            }

            if(isset($data["data"][0]["Engine_Location"])){
                $arr["Engine_Location"]=$data["data"][0]["Engine_Location"];
            }

            if(isset($data["data"][0]["Engine_Type"])){
                $arr["Engine_Type"]=$data["data"][0]["Engine_Type"];
            }

            if(isset($data["data"][0]["Engine_InhaleType"])){
                $arr["Engine_InhaleType"]=$data["data"][0]["Engine_InhaleType"];
            }

            if(isset($data["data"][0]["Engine_horsepower"])){
                $arr["Engine_horsepower"]=$data["data"][0]["Engine_horsepower"];
            }

            if(isset($data["data"][0]["Engine_MaxNJ"])){
                $arr["Engine_MaxNJ"]=$data["data"][0]["Engine_MaxNJ"];
            }

            if(isset($data["data"][0]["Engine_EnvirStandard"])){
                $arr["Engine_EnvirStandard"]=$data["data"][0]["Engine_EnvirStandard"];
            }
            if(isset($data["data"][0]["OutSet_Height"])){
                $arr["OutSet_Height"]=$data["data"][0]["OutSet_Height"];
            }

            if(isset($data["data"][0]["OutSet_Length"])){
                $arr["OutSet_Length"]=$data["data"][0]["OutSet_Length"];
            }

            if(isset($data["data"][0]["OutSet_MinGapFromEarth"])){
                $arr["OutSet_MinGapFromEarth"]=$data["data"][0]["OutSet_MinGapFromEarth"];
            }

            if(isset($data["data"][0]["OutSet_WheelBase"])){
                $arr["OutSet_WheelBase"]=$data["data"][0]["OutSet_WheelBase"];
            }

            if(isset($data["data"][0]["OutSet_Width"])){
                $arr["OutSet_Width"]=$data["data"][0]["OutSet_Width"];
            }

            if(isset($data["data"][0]["Oil_FuelCapacity"])){
                $arr["Oil_FuelCapacity"]=$data["data"][0]["Oil_FuelCapacity"];
            }

            if(isset($data["data"][0]["Oil_FuelTab"])){
                $arr["Oil_FuelTab"]=$data["data"][0]["Oil_FuelTab"];
            }

            if(isset($data["data"][0]["Oil_FuelType"])){
                $arr["Oil_FuelType"]=$data["data"][0]["Oil_FuelType"];
            }

            if(isset($data["data"][0]["Oil_SupplyType"])){
                $arr["Oil_SupplyType"]=$data["data"][0]["Oil_SupplyType"];
            }

            $int_res =  yield $this->DetailModel->insert_detail($arr);

            if($int_res){
                $res = yield $this->redis_pool->getCoroutine()->sAdd('details',$model_id);

//                $log = date('Y-m-d h:i:s',time())."Detail ADD : ".$model_id;
//                $this->log->info($log);
//
//                print_r($log);
//                print_r('/n');
            }
        }

    }

    public function getmodelsdetail($model_id=116661){


        $url="http://carapi.ycapp.yiche.com/Car/GetCarStylePropertys?carIds=".$model_id;
        $html=file_get_contents($url);
        $data=json_decode($html,true);

        $arr=array();
        $tablename="bibi_car_model_detail";
        $arr["model_id"]=$model_id;
        if(isset($data["data"][0]["CarReferPrice"])){
            $arr["CarReferPrice"]=$data["data"][0]["CarReferPrice"];
        }

        if(isset($data["data"][0]["Car_RepairPolicy"])){
            $arr["Car_RepairPolicy"]=$data["data"][0]["Car_RepairPolicy"];
        }

        if(isset($data["data"][0]["Engine_ExhaustForFloat"])){
            $arr["Engine_ExhaustForFloat"]=$data["data"][0]["Engine_ExhaustForFloat"];
        }

        if(isset($data["data"][0]["UnderPan_ForwardGearNum"])){
            $b = '手动';
            if(strpos($data["data"][0]["UnderPan_ForwardGearNum"], $b) !== false ){
                //包含
                $arr["UnderPan_ForwardGearNum_type"]=1;
            }else{
                $arr["UnderPan_ForwardGearNum_type"]=2;
            }
            $arr["UnderPan_ForwardGearNum"]=$data["data"][0]["UnderPan_ForwardGearNum"];
        }

        if(isset($data["data"][0]["Perf_ZongHeYouHao"])){
            $arr["Perf_ZongHeYouHao"]=$data["data"][0]["Perf_ZongHeYouHao"];
        }

        if(isset($data["data"][0]["Perf_AccelerateTime"])){
            $arr["Perf_AccelerateTime"]=$data["data"][0]["Perf_AccelerateTime"];
        }

        if(isset($data["data"][0]["Perf_MaxSpeed"])){
            $arr["Perf_MaxSpeed"]=$data["data"][0]["Perf_MaxSpeed"];
        }

        if(isset($data["data"][0]["Perf_SeatNum"])){
            $arr["Perf_SeatNum"]=$data["data"][0]["Perf_SeatNum"][0];
        }

        if(isset($data["data"][0]["Perf_DriveType"])){
            $arr["Perf_DriveType"]=$data["data"][0]["Perf_DriveType"];
        }

        if(isset($data["data"][0]["Engine_Location"])){
            $arr["Engine_Location"]=$data["data"][0]["Engine_Location"];
        }

        if(isset($data["data"][0]["Engine_Type"])){
            $arr["Engine_Type"]=$data["data"][0]["Engine_Type"];
        }

        if(isset($data["data"][0]["Engine_InhaleType"])){
            $arr["Engine_InhaleType"]=$data["data"][0]["Engine_InhaleType"];
        }

        if(isset($data["data"][0]["Engine_horsepower"])){
            $arr["Engine_horsepower"]=$data["data"][0]["Engine_horsepower"];
        }

        if(isset($data["data"][0]["Engine_MaxNJ"])){
            $arr["Engine_MaxNJ"]=$data["data"][0]["Engine_MaxNJ"];
        }

        if(isset($data["data"][0]["Engine_EnvirStandard"])){
            $arr["Engine_EnvirStandard"]=$data["data"][0]["Engine_EnvirStandard"];
        }
        if(isset($data["data"][0]["OutSet_Height"])){
            $arr["OutSet_Height"]=$data["data"][0]["OutSet_Height"];
        }

        if(isset($data["data"][0]["OutSet_Length"])){
            $arr["OutSet_Length"]=$data["data"][0]["OutSet_Length"];
        }

        if(isset($data["data"][0]["OutSet_MinGapFromEarth"])){
            $arr["OutSet_MinGapFromEarth"]=$data["data"][0]["OutSet_MinGapFromEarth"];
        }

        if(isset($data["data"][0]["OutSet_WheelBase"])){
            $arr["OutSet_WheelBase"]=$data["data"][0]["OutSet_WheelBase"];
        }

        if(isset($data["data"][0]["OutSet_Width"])){
            $arr["OutSet_Width"]=$data["data"][0]["OutSet_Width"];
        }

        if(isset($data["data"][0]["Oil_FuelCapacity"])){
            $arr["Oil_FuelCapacity"]=$data["data"][0]["Oil_FuelCapacity"];
        }

        if(isset($data["data"][0]["Oil_FuelTab"])){
            $arr["Oil_FuelTab"]=$data["data"][0]["Oil_FuelTab"];
        }

        if(isset($data["data"][0]["Oil_FuelType"])){
            $arr["Oil_FuelType"]=$data["data"][0]["Oil_FuelType"];
        }

        if(isset($data["data"][0]["Oil_SupplyType"])){
            $arr["Oil_SupplyType"]=$data["data"][0]["Oil_SupplyType"];
        }

        print_r($arr);



    }


    public function getsebyurl(){


    }







}