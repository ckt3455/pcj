<?php

namespace backend\models;

use common\components\CommonFunction;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods_option}}".
 *
 * @property integer $id
 * @property string $specs
 * @property string $title
 * @property integer $goods_id
 * @property string $cover
 * @property integer $sales
 * @property string $booking_price
 * @property string $price
 * @property integer $mall_sales
 * @property string $discount
 * @property string $upc_code
 * @property integer $status
 * @property integer $append
 * @property integer $updated
 */
class GoodsOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['specs', 'title', 'goods_id'], 'required'],
            [['stock','goods_id', 'status', 'append', 'updated'], 'integer'],
            [['price'], 'number'],
            [['title', 'thumb', 'upc_code'], 'string', 'max' => 255],
            [['specs','specs_search'],'safe'],

            [['crossed_price', 'weight'], 'number'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'specs' => '规格',
            'specs_search' => '规格搜索',//规格ID：规格参数ID，规格ID：规格参数ID，
            'title' => '规格说明',
            'goods_id' => '所属商品',
            'thumb' => '封面',
            'price' => '零售价',
            'upc_code' => '商品条码',
            'status' => '是否启用',
            'append' => '添加时间',
            'updated' => '修改时间',
            'crossed_price' => '划线价',
            'weight' => '重量',
            'stock' => '库存',
        ];
    }

    /**
    * @return array
    */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['append', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
            ],
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     * 自动插入
     */
    public function beforeSave($insert)
    {
        if(is_array($this->specs)){
            asort($this->specs);
            $this->specs = implode(',',$this->specs);
        }
        if(is_array($this->specs_search)){
            asort($this->specs_search);
            $this->specs_search = implode(',',$this->specs_search);
        }
        return parent::beforeSave($insert);
    }


    /**
     * 关联属性
     */
    public function getGoods(){
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getCover(){
        if(empty($this->thumb)){
            return CommonFunction::setImg('\Public\index\images\default_cover.png');
        }
        return CommonFunction::setImg($this->thumb);
    }
    public $specsArr;
    public function getSpecsArr(){
        if(empty($this->specsArr)){

            $specs_search = explode(',',$this->specs_search);
            $specsArr = array();
            foreach ($specs_search as $v){
                $v = explode(':',$v);
                $specsArr[]=array(
                    'specs_id'=>$v[0],
                    'specs_title'=>GoodsSpec::getName($v[0]),
                    'specs_item_id'=>$v[1],
                    'specs_item_title'=>GoodsSpecItem::getName($v[1]),
                );
            }
            $this->specsArr = $specsArr;
        }
        return $this->specsArr;
    }
    public function getSpecsText(){
        $specsArr = $this->getSpecsArr();
        if(!empty($specsArr)){
            $title = '';
            foreach ($specsArr as $v){
                $title .= empty($title)?$v['specs_title'].':'.$v['specs_item_title']:'；'.$v['specs_title'].':'.$v['specs_item_title'];
            }
            return $title;
        }
        return '';
    }
    /**
     * @param $stock
     * @return bool
     * User:五更的猫
     * DateTime:2025/9/1 15:41
     * TODO 设置库存
     */
    public function setStock($stock){

        if(!empty($stock) && is_numeric($stock) && $this->stock>=$stock){

            if($this->updateCounters(['stock'=>$stock*-1])) {
                Goods::SetStock($this->goods_id);
                return true;
            }
        }
        return false;
    }

    /**
     * @param $goods_id
     * @return int|mixed
     * User:五更的猫
     * DateTime:2025/8/11 16:09
     * TODO 重量
     */
    public static function getWeight($id){
        $model = self::find()->where(['id'=>$id])->one();

        return !empty($model)?$model['weight']:0;
    }


    public static function getStock($gid){
        $model = self::find()->where(['goods_id'=>$gid])->sum('stock');

        return $model;
    }

    /**
     * @param $goods_id 商品ID
     * @param $specs_data 规格数组
     * @param $price_data 规格价格数组
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @User:五更的猫
     * @DateTime: 2024/7/19 11:17
     * @TODO 添加商品规格
     */
    public static function SetData($goods_id,$specs_data,$price_data){

        $attribute = GoodsSpec::SetData($specs_data);

        /*$price_data
             * array(
                array(

                    'price'=>'零售价',
                    'thumb'=>'封面',
                    'upc_code'=>'商品条码',
                    'crossed_price'=>'划线价',
                    'weight'=>'重量',
                    'stock' => '库存',
                    'specs' => array(
                        array(
                            'title'=>'规格',
                            'option'=>'规格选项',
                        )
                    ),
                )
            );*/

        /*$specs_data
         * array(
            array(
                'title'=>'规格',
                'list'=>'规格选项列表',
            )
        )*/
        $skuArr=array();

        foreach ($price_data as $v){
            $gvidArr = array();
            $txtArr = array();
            $specs_searchArr = array();
            foreach ($v['specs'] as $v2){
                if(!isset($attribute[$v2['title']])){
                    continue 2;
                }
                if(!isset($attribute[$v2['title']]['list'][$v2['option']])){
                    continue 2;
                }
                $txtArr[] = $v2['option'];
                $gvidArr[] = $attribute[$v2['title']]['list'][$v2['option']];
                $specs_searchArr[]=$attribute[$v2['title']]['id'].':'.$attribute[$v2['title']]['list'][$v2['option']];
            }

            $txtArr = implode('_',$txtArr);

            asort($gvidArr);
            $gvidArr = implode(',',$gvidArr);
            asort($specs_searchArr);
            $specs_searchArr = implode(',',$specs_searchArr);

            $model = self::find()->where(['specs'=>$gvidArr,'goods_id'=>$goods_id])->one();
            if(empty($model)){
                $model = new GoodsOption();
                $model->goods_id = $goods_id;
            }
            $model->specs = $gvidArr;
            $model->specs_search = $specs_searchArr;
            $model->title = $txtArr;
            $model->price = (float)$v['price'];
            $model->upc_code = (string)$v['upc_code'];

            $model->thumb = (string)CommonFunction::unsetImg($v['thumb']);
            $model->crossed_price = (float)$v['crossed_price'];
            $model->weight = (float)$v['weight'];
            $model->stock = (int)$v['stock'];

            if($model->save()){
                $skuArr[] = $model['id'];
            }
        }
        $data = self::find()->where(['and',['NOT IN','id',$skuArr],['goods_id'=>$goods_id]])->all();
        foreach ($data as $v){
            $v->delete();
        }
        //self::deleteAll(['and',['NOT IN','id',$skuArr],['gid'=>$goods_id]]);
        return $skuArr;
    }
    public function getSpec(){
        $specItem = GoodsSpecItem::find()->where('id in('.$this->specs.')')->all();

        $str = '';
        foreach ($specItem as $v){
            $str .=$v->goodsSpec->title."：".$v->title.'；';
        }

        return $str;
    }

    /**
     * @param $goods_id
     * @return true
     * User:五更的猫
     * DateTime:2025/3/27 11:43
     * TODO 删除规格
     */
    public static function DelData($goods_id){
        $data = self::find()->where(['goods_id'=>$goods_id])->all();
        foreach ($data as $v){
            $v->delete();
        }
        return true;
    }


    /**
     * @param $goods_id 商品ID
     * @param $specs_data 规格数组
     * @param $price_data 规格价格数组
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @User:五更的猫
     * @DateTime: 2024/7/19 11:17
     * @TODO 添加商品规格
     */
    public static function EditData($goods_id,$price_data){

        /*$price_data
              * array(
                 array(
                     'id' => '记录ID',
                     'price'=>'零售价',
                     'cover'=>'封面',
                     'upc_code'=>'商品条码',
                     'crossed_price'=>'划线价',
                     'weight'=>'重量',
                 )
             );*/

        $skuArr=array();

        foreach ($price_data as $v){

            $model = self::find()->where(['id'=>$v['id'],'goods_id'=>$goods_id])->one();
            if(empty($model)){
                return false;
            }
            $model->price = (float)$v['price'];
            $model->thumb = CommonFunction::unsetImg($v['thumb']);
            $model->upc_code = $v['upc_code'];
            $model->crossed_price = (float)$v['crossed_price'];
            $model->weight = (float)$v['weight'];
            $model->stock = (int)$v['stock'];

            if($model->save()){
                $skuArr[] = $model['id'];
            }
        }

        return $skuArr;
    }

}
