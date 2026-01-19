<?php

namespace backend\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserMoneyApply;

/**
 * UserMoneyApplySearch represents the model behind the search form about `backend\models\UserMoneyApply`.
 */
class UserMoneyApplySearch extends UserMoneyApply
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'buyer_id', 'status', 'type', 'apply_type', 'created_at', 'updated_at'], 'integer'],
            [['money', 'fee'], 'number'],
            [['zfb_number', 'zfb_name', 'zfb_image', 'wx_number', 'wx_name', 'wx_image', 'bank_number', 'bank_name', 'bank_open'], 'safe'],
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
        $query = UserMoneyApply::find();

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
            'user_id' => $this->user_id,
            'buyer_id' => $this->buyer_id,
            'money' => $this->money,
            'fee' => $this->fee,
            'status' => $this->status,
            'type' => $this->type,
            'apply_type' => $this->apply_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'zfb_number', $this->zfb_number])
            ->andFilterWhere(['like', 'zfb_name', $this->zfb_name])
            ->andFilterWhere(['like', 'zfb_image', $this->zfb_image])
            ->andFilterWhere(['like', 'wx_number', $this->wx_number])
            ->andFilterWhere(['like', 'wx_name', $this->wx_name])
            ->andFilterWhere(['like', 'wx_image', $this->wx_image])
            ->andFilterWhere(['like', 'bank_number', $this->bank_number])
            ->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'bank_open', $this->bank_open]);

        if (!empty($this->start_time)) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->start_time)]);
        }
        if (!empty($this->end_time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($this->end_time) + 24 * 3600 - 1]);
        }

        return $dataProvider;
    }
}
