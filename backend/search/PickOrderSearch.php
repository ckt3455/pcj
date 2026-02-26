<?php

namespace backend\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PickOrder;

/**
 * PickOrderSearch represents the model behind the search form of `backend\models\PickOrder`.
 */
class PickOrderSearch extends PickOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'address_id', 'status', 'audit_user_id', 'created_at', 'updated_at', 'audit_time', 'pick_time', 'is_delete'], 'integer'],
            [['pick_number', 'consignee', 'phone', 'province', 'city', 'area', 'address_detail', 'content'], 'safe'],
            [['total_amount'], 'number'],
        ];
    }

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
        $query = PickOrder::find();

        // add conditions that should always apply here
        $query->andWhere(['is_delete' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            'address_id' => $this->address_id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'audit_user_id' => $this->audit_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'audit_time' => $this->audit_time,
            'pick_time' => $this->pick_time,
            'is_delete' => $this->is_delete,
        ]);

        $query->andFilterWhere(['like', 'pick_number', $this->pick_number])
            ->andFilterWhere(['like', 'consignee', $this->consignee])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'address_detail', $this->address_detail])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
