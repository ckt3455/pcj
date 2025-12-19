<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%freight_model}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $is_default
 * @property integer $type
 * @property integer $sort
 */
class FreightModel extends \yii\db\ActiveRecord
{
    public static $type = [
        1=>'按重量计费',
        2=>'按件计费'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%freight_model}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['is_default', 'type', 'sort','status'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['first','first_money','next','next_money'],'number'],
            //['type','validateType'],
            [['content'],'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'is_default' => '是否默认',
            'type' => '计费方式',
            'sort' => '排序',
            'content'=>'详细介绍',
            'status'=>'是否使用',
            'first'=>'首重(千克)/首件',
            'first_money'=>'首费(元)',
            'next'=>'续重(千克)/续件',
            'next_money'=>'续费(元)',
        ];
    }

    //保存后事件
    public function afterSave($insert, $changedAttributes)
    {
        //$changedAttributes  要改变的字段，未改变的值
        //$this->字段名  改变保存的值
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            //新增处理

        }else{
            //修改处理
        }
        if($this->is_default==1){
            self::updateAll(['is_default'=>0],['and',['is_default'=>1],['<>','id',$this->id]]);
        }
    }

    /*public function validateType(){
        if (!$this->hasErrors()) {
            if($this->is_default==1){
                FreightModel::updateAll(['is_default'=>0],['is_default'=>1]);
            }
        }
    }*/



    /**
     * 关联详情
     */
    public function getDetail(){
        return $this->hasMany(Freight::className(), ['model_id' => 'id']);
    }

    /**
     * 重量,件数计算运费
     */
    public static function Freight($weight,$freight_id,$number,$city_id){
        $money=0;
        $freight=FreightModel::findOne($freight_id);
        if($freight){
            if($freight->type==1 and $weight>0){
                //按重量计费
                $model=Freight::find()->where(['model_id'=>$freight->id])->andWhere(['like','city_id',$city_id])->one();
                if($model){
                    if($weight<=$model->first){
                        $money=$model->first_money;
                    }else{
                        $money=$model->first_money+ceil(($weight-$model->first)/$model->next)*$model->next_money;
                    }
                }else{
                    if($weight<=$freight->first){
                        $money=$freight->first_money;
                    }else{
                        $money=$freight->first_money+ceil(($weight-$freight->first)/$freight->next)*$freight->next_money;
                    }

                }
            }
            if($freight->type==2 and $number>0){
                //按件数计费
                $model=Freight::find()->where(['model_id'=>$freight->id])->andWhere(['like','city_id',$city_id])->one();
                if($model){
                    if($number<=$model->first){
                        $money=$model->first_money;
                    }else{
                        $money=$model->first_money+ceil(($number-$model->first)/$model->next)*$model->next_money;
                    }


                }else{
                    if($number<=$freight->first){

                        $money=$freight->first_money;
                    }else{
                        $money=$freight->first_money+ceil(($number-$freight->first)/$freight->next)*$freight->next_money;
                    }
                }

            }
        }
        return $money;
    }

    public static function getGoodsFreight(array $goods,$city_id){
        $freightData = array();
        foreach ($goods as $v){
            $goodsModel = Goods::find()->where(['id'=>$v['goods_id']])->one();
            if(empty($goodsModel)){
                return 0;
            }
            //没有设置无法直接使用
            if (!isset($freightData[$goodsModel->freight_model_id])) {
                $freightData[$goodsModel->freight_model_id] = array(
                    'weight' => 0,
                    'number' => 0
                );
            }
            if(!empty($v['option_id'])){
                $goodsOptionModel = GoodsOption::find()->where(['id'=>$v['option_id'],'goods_id'=>$v['goods_id']])->one();
                if(empty($goodsOptionModel)){
                    return 0;
                }
                $freightData[$goodsModel->freight_model_id]['weight'] += $goodsOptionModel->weight * $v['total'];
                $freightData[$goodsModel->freight_model_id]['number'] += $v['total'];
            }else{
                $freightData[$goodsModel->freight_model_id]['weight'] += $goodsModel->weight * $v['total'];
                $freightData[$goodsModel->freight_model_id]['number'] += $v['total'];
            }
        }

        $default_id = self::getDefault();

        $price = 0;
        foreach ($freightData as $k=>$v){
            if($k==0){
                $price += self::Freight($v['weight'],$default_id,$v['number'],$city_id);
            }else{
                $price += self::Freight($v['weight'],$k,$v['number'],$city_id);
            }
        }

        return $price;
    }

    public static function getGoodsFreight22(array $goods,$city_id){
        $goodsIds = array();
        foreach ($goods as $k=>$v){
            $goodsIds[] = $k;
        }

        $goodsModel = GoodsOption::find()->where(['in', 'id', $goodsIds])->all();

        $freightData = array();
        foreach ($goodsModel as $v){
            //没有设置无法直接使用
            if (!isset($freightData[$v->goods->freight_model_id])) {
                $freightData[$v->goods->freight_model_id] = array(
                    'weight' => 0,
                    'number' => 0
                );
            }
            $freightData[$v->goods->freight_model_id]['weight'] += $v->weight * $goods[$v->id]['total'];
            $freightData[$v->goods->freight_model_id]['number'] += $goods[$v->id]['total'];
        }
        $default_id = self::getDefault();

        $price = 0;
        foreach ($freightData as $k=>$v){
            if($k==0){
                $price += self::Freight($v['weight'],$default_id,$v['number'],$city_id);
            }else{
                $price += self::Freight($v['weight'],$k,$v['number'],$city_id);
            }
        }

        return $price;
    }
    /**
     * 重量,件数计算运费
     */
    public static function WeightFreight($weight,$freight_id,$city_id){
        $money=0;
        $freight=FreightModel::findOne($freight_id);
        if($freight){
            if($weight>0){
                //按重量计费
                $model=Freight::find()->where(['model_id'=>$freight->id])->andWhere(['like','city_id',$city_id])->one();
                if($model){
                    if($weight<=$model->first){
                        $money=$model->first_money;
                    }else{
                        $money=$model->first_money+ceil(($weight-$model->first)/$model->next)*$model->next_money;
                    }
                }else{
                    if($weight<=$freight->first){
                        $money=$freight->first_money;
                    }else{
                        $money=$freight->first_money+ceil(($weight-$freight->first)/$freight->next)*$freight->next_money;
                    }
                }
            }
        }
        return $money;
    }
    /**
     * 获取配送列表
     */
    public static function getList(){

        $model=FreightModel::find()->all();
        $arr[0]['id'] = 0;
        $arr[0]['title'] = '无';
        foreach($model as $key => $value){
            $arr[$key+1]['id'] = $value['id'];
            $arr[$key+1]['title'] = $value['title'];
        }
        return ArrayHelper::map($arr,'id','title');
    }

    public static function getTitle($id)
    {
        $model = self::findOne($id);
        if($model)
        {
            return $model['title'];
        }
        else
        {
            return "暂无";
        }
    }

    public static function getName($id){
        $model = self::findOne($id);
        if($model)
        {
            return $model['title'];
        }else{
            $model = self::findOne(['is_default'=>1]);
            if($model){
                return $model['title'];
            }
        }

        return "暂无";

    }

    public static function getDefault(){
        $model = self::findOne(['is_default'=>1]);
        if(!empty($model)){
            return $model->id;
        }
        return 0;
    }

}
