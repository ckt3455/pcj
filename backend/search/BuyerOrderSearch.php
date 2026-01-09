<?php

namespace backend\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BuyerOrder;

/**
 * BuyerOrderSearch represents the model behind the search form about `backend\models\BuyerOrder`.
 */
class BuyerOrderSearch extends BuyerOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'buyer_id', 'user_id', 'type', 'status', 'pay_type', 'created_at', 'updated_at', 'paid_time', 'parent_id', 'level', 'audit_time'], 'integer'],
            [['order_number'], 'safe'],
            [['money', 'discount', 'total_money'], 'number'],
            [['start_time','end_time'],'safe']
        ];
    }

    public  $start_time;
    public  $end_time;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BuyerOrder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'id'=>SORT_DESC,
                    ]
                ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'buyer_id' => $this->buyer_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'pay_type' => $this->pay_type,
            'money' => $this->money,
            'discount' => $this->discount,
            'total_money' => $this->total_money,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'paid_time' => $this->paid_time,
            'parent_id' => $this->parent_id,
            'level' => $this->level,
            'audit_time' => $this->audit_time,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number]);

        if (!empty($this->start_time)) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->start_time)]);
        }
        if (!empty($this->end_time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($this->end_time) + 24 * 3600 - 1]);
        }

        return $dataProvider;
    }
}
