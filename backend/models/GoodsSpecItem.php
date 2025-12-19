<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods_spec_item}}".
 *
 * @property integer $id
 * @property integer $spec_id
 * @property string $title
 * @property integer $append
 * @property integer $updated
 */
class GoodsSpecItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_spec_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spec_id', 'title'], 'required'],
            [['spec_id', 'append', 'updated','sort'], 'integer'],
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
            'spec_id' => '所属商品规格',
            'title' => '属性名称',
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
     * @param $gaid  所属属性
     * @param $name  属性值
     * 添加商品属性值
     */
    public static function SetData($spec_id,$title){
        $title = !is_array($title)?explode(',',$title):$title;

        $gvidArr = array();
        foreach ($title as $k=>$v){
            $model = self::find()->where(['title'=>$v,'spec_id'=>$spec_id])->one();
            if(empty($model)){
                $model = new GoodsSpecItem();
                $model->spec_id = $spec_id;
                $model->title = $v;
                $model->save();
            }
            $gvidArr[$v] = $model['id'];
        }
        return $gvidArr;
    }

    /**
     * 关联属性
     */
    public function getGoodsSpec(){
        return $this->hasOne(GoodsSpec::className(), ['id' => 'spec_id']);
    }
    /**
     * @param array $idArr
     *
     * @return array
     * 获取属性
     */
    public static function GetSku($idArr = array()){
        $idArr = is_array($idArr)?$idArr:explode(',',$idArr);
        $model = self::find()->where(['in','id',$idArr])->all();

        $skuData = array();
        foreach ($model as $k=>$v){

            $skuData[$v['spec_id']]['id'] = $v['spec_id'];
            $skuData[$v['spec_id']]['checked'] = '-1';
            $skuData[$v['spec_id']]['name'] = $v->goodsSpec->title;
            $skuData[$v['spec_id']]['list'][] = array(
                'id'=>$v['id'],
                'title'=>$v['title'],
            );
        }

        return array_values($skuData);

    }

    /**
     * @param $idArr
     *
     * @return array
     * 获取属性 属性值
     */
    public static function GetNameArr($idArr){
        $name = array();
        if(!empty($idArr)) {
            $idArr = is_array($idArr) ? $idArr : explode(',', $idArr);
            $model = self::find()->where(['in', 'id', $idArr])->all();

            foreach ($model as $k => $v) {

                $name[] = $v->goodsSpec->title . '：' . $v['title'];
            }
        }
        return $name;
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
        return '未知规格值';
    }
}
