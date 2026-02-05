<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%buyer}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $level_id
 * @property float|null $goods_money
 * @property float|null $money
 * @property string|null $code
 * @property string|null $title
 * @property string|null $province
 * @property string|null $city
 * @property string|null $area
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Buyer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'province', 'city', 'area'], 'default', 'value' => null],
            [['money'], 'default', 'value' => 0.00],
            [['user_id', 'level_id', 'created_at', 'updated_at'], 'integer'],
            [['goods_money', 'money'], 'number'],
            [['code'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 100],
            [['province', 'city', 'area'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                // 移除 updated_at 默认值设置，避免与行为冲突
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'level_id' => '等级',
            'title' => '供货商名称', // 保持一致性
            'goods_money' => '货款',
            'money' => '余额',
            'code' => 'Code',
            'province' => '省',
            'city' => '市',
            'area' => '区',
            'created_at' => '添加时间',
            'updated_at' => '更新时间', // 更直观的标签
        ];
    }

    /**
     * 获取关联的等级信息
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(BuyerLevel::className(), ['id' => 'level_id']);
    }

    /**
     * 获取工人数量
     * @return int
     */
    public function getWorkerCount()
    {
        // 确保 id 合法性，并缓存查询结果以提升性能
        if (!isset($this->_workerCount)) {
            $this->_workerCount = (int) BuyerWorker::find()
                ->where(['buyer_id' => $this->id])
                ->count();
        }
        return $this->_workerCount;
    }

    // 缓存变量
    private $_workerCount;
}
