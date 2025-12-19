<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods_spec}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $append
 * @property integer $updated
 */
class GoodsSpec extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_spec}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['append', 'updated','sort'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '名称',
            'sort' => '排序',
            'append' => '添加时间',
            'updated' => '修改时间',
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
     * 关联属性值
     */
    public function getGoodsSpecItem(){
        return $this->hasMany(GoodsSpecItem::className(), ['spec_id' => 'id']);
    }

    /**
     * @param $goods_id  商品ID
     * @param $title  属性
     * @param $group  属性值
     * 添加商品属性
     */
    public static function SetData($data){
        /*$data = array(
               array(
                   'title'=>'规格',
                   'list'=>'规格选项数组',
               )
           )*/
        $gaidArr = array();
        $listArr = array();
        foreach ($data as $k=>$v){
            $model = self::find()->where(['title'=>$v['title']])->one();
            if(empty($model)){
                $model = new GoodsSpec();
                $model->title = $v['title'];
                $model->save();
            }
            $gaidArr[$v['title']] = $model['id'];
            $listArr[$v['title']] = array(
                'id'=>$model['id'],
                'list'=>GoodsSpecItem::SetData($model['id'],$v['list'])
            );
        }

        return $listArr;

    }

    public static function getData($goods_id){

        $models = self::find()->where(['goods_id'=>$goods_id])->all();

        $data = array();
        foreach ($models as $v){
            if(isset($v->goodsSpecItem)){
                $list = array();
                foreach ($v->goodsSpecItem as $v2){
                    $list[] = array(
                        'id'=>(int)$v2->id,
                        'value'=>$v2->title,
                    );
                }
                $data[] = array(
                    'id'=>(int)$v->id,
                    'value'=>$v->title,
                    'list'=>$list,
                );
            }
        }
        return $data;
    }

    public static function getName($id)
    {
        if($id==0){
            return '无';
        }
        $model = self::findOne($id);
        if(!empty($model)) {
            return $model->title;
        }
        return '未知规格';
    }

}
