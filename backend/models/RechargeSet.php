<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%recharge_set}}".
 *
 * @property int $id
 * @property float|null $money 充值金额
 * @property float|null $give_money 赠送金额
 */
class RechargeSet extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%recharge_set}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money', 'give_money'], 'default', 'value' => null],
            [['money', 'give_money'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'money' => 'Money',
            'give_money' => 'Give Money',
        ];
    }

}
