<?php

namespace backend\models;

use common\components\CommonFunction;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $thumb
 * @property string $thumbs
 * @property integer $category_id
 * @property integer $has_option
 * @property string $price
 * @property string $crossed_price
 * @property integer $sales
 * @property string $content
 * @property integer $status
 * @property integer $sort
 * @property string $upc_code
 * @property string $intro
 * @property string $weight
 * @property string $units
 * @property integer $stock
 * @property integer $stock_warning
 * @property string $score
 * @property string $hot
 * @property string $associated_goods
 * @property integer $freight_model_id
 * @property integer $append
 * @property integer $updated
 * @property integer $is_del
 */
class Goods extends \yii\db\ActiveRecord
{
    public static $has_option = [
        2=>'无',
        1=>'多规格商品',
    ];

    public static $hot = [
        1=>'今日爆品',
        2=>'为你推荐',
    ];
    public static $status = [
        1=>'上架',
        2=>'下架',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'thumb', 'category_id', 'units'], 'required'],
            [['content'], 'string'],
            [['category_id', 'has_option', 'sales', 'status', 'sort', 'stock', 'stock_warning', 'freight_model_id', 'append', 'updated', 'is_del'], 'integer'],
            [['price', 'crossed_price', 'weight', 'score'], 'number'],
            [['thumb_video','title', 'sub_title', 'thumb', 'intro'], 'string', 'max' => 255],
            [['upc_code'], 'string', 'max' => 50],
            [['units'], 'string', 'max' => 10],
            [['thumbs', 'associated_goods', 'hot'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '商品名称',
            'sub_title' => '副标题',
            'thumb_video' => '封面视频',
            'thumb' => '封面',
            'thumbs' => '商品轮播图',
            'category_id' => '分类',
            'has_option' => '规格类型',
            'price' => '零售价',
            'crossed_price' => '划线价',
            'sales' => '商品销量',
            'content' => '商品详情',
            'status' => '商品状态',
            'sort' => '排序',
            'upc_code' => '商品条码',
            'intro' => '简介',
            'weight' => '重量',
            'units' => '售卖单位',
            'stock' => '库存',
            'stock_warning' => '库存预警数量',
            'score' => '评分',
            'hot' => '推荐位置',
            'associated_goods' => '关联推荐商品',
            'freight_model_id' => '快递模版',
            'append' => '添加时间',
            'updated' => '修改时间',
            'is_del' => '是否删除',
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
        if(is_array($this->thumbs)){
            $this->thumbs = implode(',',$this->thumbs);
        }
        if(is_array($this->associated_goods)){
            $this->associated_goods = implode(',',$this->associated_goods);
        }
        if(is_array($this->hot)){
            $this->hot = implode(',',$this->hot);
        }

        return parent::beforeSave($insert);
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
    }

    public function SetMinPrice(){

        if($this->has_option==1){

            $model = GoodsOption::find()->where(['goods_id'=>$this->id])->orderBy('price asc')->one();

            if(!empty($model)){

                $this->price = $model->price;
                $this->upc_code = $model->upc_code;
                $this->crossed_price = $model->crossed_price;
                $this->weight = $model->weight;

                return $this->save();
            }
        }
        return true;
    }
    public function Del(){

        $this->is_del = 1;

        return $this->save();
    }

    public $SpecData = array();

    /**
     * @return array|mixed
     * User:五更的猫
     * DateTime:2025/3/7 14:52
     * TODO 规格参数
     */
    public function getSpecData(){

        if($this->has_option==2){
            return array();
        }

        if(!empty($this->SpecData)){
            return $this->SpecData;
        }

        $models = GoodsOption::find()->andWhere(['goods_id'=>$this->id])->all();

        $arr = array();
        $SpecIds = array();
        foreach ($models as $v){
            $str = explode(',',$v['specs_search']);

            foreach ($str as $v2){
                $str2 = explode(':',$v2);
                $arr[$str2[0]][] = $str2[1];
                $SpecIds[]=$str2[0];
            }
        }
        if(empty($SpecIds)){
            return array();
        }

        $list = array();

        //->orderBy('sort asc,title asc,id desc')
        $SpecList = GoodsSpec::find()->andWhere(['in','id',$SpecIds])->orderBy('id asc')->select('id,title')->asArray()->all();

        foreach ($SpecList as $v){
            $v['list'] = GoodsSpecItem::find()->andWhere(['in','id',$arr[$v['id']]])->andWhere(['spec_id'=>$v['id']])->orderBy('sort asc,title asc,id desc')->select('id,title')->asArray()->all();
            $list[]=$v;
        }

        $this->SpecData = $list;

        return $this->SpecData;
    }

    /**
     * @param $goods_id
     * @return bool
     * User:五更的猫
     * DateTime:2025/8/19 16:42
     * TODO 更新库存
     */
    public static function SetStock($goods_id){
        $model = self::find()->where(['id'=>$goods_id])->one();
        if(!empty($model)){
            if($model->has_option==1){
                $model->stock = GoodsOption::getStock($goods_id);
                return $model->save();
            }
        }
        return false;
    }
    /**
     * @param $goods_id
     * @return bool
     * User:五更的猫
     * DateTime:2025/8/19 16:42
     * TODO 更新库存
     */
    public function updateStock($stock){
        if($this->has_option==1){
            return false;
        }
        if(!empty($stock) && is_numeric($stock) && $this->stock>=$stock){

            if($this->updateCounters(['stock'=>$stock*-1])) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param $sales
     * @return bool
     * User:五更的猫
     * DateTime:2025/9/4 9:33
     * TODO 设置销量
     */
    public function SetSales($sales){
        if(!empty($sales) && is_numeric($sales)){
            if($this->updateCounters(['sales'=>$sales])) {
                return true;
            }
        }
        return false;
    }

    public function getCover(){
        if(empty($this->thumb)){
            return CommonFunction::setImg('\Public\index\images\default_cover.png');
        }
        return CommonFunction::setImg($this->thumb);
    }

    public static function getList($status=null){
        return Goods::find()->andFilterWhere(['status'=>$status])->orderBy('sort asc,id desc')->indexBy('id')->select('title')->column();
    }
    public static function GetList2($id){
        $list = array();
        if(!empty($id)){

            if(!is_array($id)){
                $id = explode(',',$id);
            }

            $models = self::find()->andFilterWhere(['in','id',$id])
                ->select('id,title')
                ->orderBy('id')
                ->asArray()
                ->all();

            $list = array();
            foreach ($models as $v){
                $list[$v['id']]=$v['title'];
            }
        }
        return $list;
    }
    public static function getName($id){
        if(empty($id)){
            return '无';
        }
        $model = Goods::findOne(['id'=>$id]);
        return empty($model)?'未知商品':$model->title;

    }

    public $MinOption;
    /**
     * @return array|mixed|null|ActiveRecord
     * User:五更的猫
     * DateTime:2020/8/26 16:26
     * TODO 获取最低价规格
     */
    public function getMinOption(){
        if(empty($this->MinOption)){
            $this->MinOption = GoodsOption::find()->where(['goods_id'=>$this->id])->orderBy('price asc')->one();
        }

        return $this->MinOption;
    }

    /**
     * @param $status
     * @return bool
     * 设置状态
     */
    public function SetStatus($status){
        //1：上架  2：下架  3：删除  4：恢复
        if(in_array($status,array(1,2,3,4))){

            //$this->status = $status;
            switch ($status){
                case 1:
                    $this->status = 1;
                    //$this->putaway_time = date('Y-m-d H:i:s');
                    break;
                case 2:
                    $this->status = 2;
                   //$this->putaway_time = date('Y-m-d H:i:s');
                    break;
                case 3:
                    $this->is_del = 1;
                    //删除  加入回收箱
                    //$this->delete_time = date('Y-m-d H:i:s');
                    break;
                case 4:
                    $this->status = 2;
                    $this->is_del = 2;
                    //$this->putaway_time = date('Y-m-d H:i:s');
                    break;
                default:
                    break;
            }
            return $this->save();
        }
        return false;
    }


    /**
     * @return bool
     * 彻底删除
     */
    public function ShiftDelete(){

        return $this->delete();
    }

    public $PriceData = array();
    public function getPriceData(){

        if($this->has_option==2){
            return array();
        }

        if(!empty($this->PriceData)){
            return $this->PriceData;
        }

        /* array(
            array(
                'price'=>'零售价',
                'thumb'=>'封面',
                'upc_code'=>'商品条码',
                'crossed_price'=>'划线价',
                'weight'=>'重量',
                'specs' => array(
                    array(
                        'title'=>'规格',
                        'option'=>'规格选项',
                    )
                ),
            )
        );*/

        $GoodsSpec = array();
        $GoodsSpecItem = array();

        foreach ($this->getSpecData() as $v){
            $GoodsSpec[$v['id']] = $v['title'];

            foreach ($v['list'] as $v2){
                $GoodsSpecItem[$v2['id']] = $v2['title'];
            }
        }

        $models = GoodsOption::find()->andWhere(['goods_id'=>$this->id])->all();

        $PriceData = array();

        foreach ($models as $v){
            $str = explode(',',$v['specs_search']);

            $specsArr = array();

            foreach ($str as $v2){
                $str2 = explode(':',$v2);

                $specsArr[]=array(
                    'title'=>$GoodsSpec[$str2[0]],
                    'option'=>$GoodsSpecItem[$str2[1]],
                );
            }
            $PriceData[]=array(
                'id'=>$v['id'],
                'price'=>$v['price'],
                'thumb'=>CommonFunction::setImg($v['thumb']),
                'upc_code'=>$v['upc_code'],
                'crossed_price'=>$v['crossed_price'],
                'weight'=>$v['weight'],
                'specs'=>$specsArr,
            );
        }

        $this->PriceData = $PriceData;

        return $this->PriceData;
    }

    public static function GetOpenCRM(){
        return (bool)Yii::$app->config->info('Open_CRM');
    }


    /**
     * @return array
     * User:五更的猫
     * DateTime:2025/9/1 16:36
     * TODO 商品轮播图
     */
    public function getThumbsData(){
        $thumbs = array();
        if(!empty($this->thumb_video)){
            $thumbs[]=array(
                'is_checked'=>1,
                'type'=>2,
                'cover'=>CommonFunction::setImg($this->thumb_video),
            );
            $thumbs[]=array(
                'is_checked'=>0,
                'type'=>1,
                'cover'=>$this->getCover(),
            );
        }else{
            $thumbs[]=array(
                'is_checked'=>1,
                'type'=>1,
                'cover'=>$this->getCover(),
            );
        }
        $thumbsData = empty($this->thumbs)?array():unserialize($this->thumbs);
        foreach ($thumbsData as $v){
            $thumbs[]=array(
                'is_checked'=>0,
                'type'=>1,
                'cover'=>CommonFunction::setImg($v)
            );
        }
        return $thumbs;
    }

    public function SalesText(){

        if($this->sales>10000){
            return round($this->sales/10000,1).'万';
        }
        return $this->sales;
    }

    public function getCategory()
    {
        return $this->hasOne(GoodsCategory::class,['id'=>'category_id']);

    }
}
