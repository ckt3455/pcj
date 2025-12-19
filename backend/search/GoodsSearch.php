<?php

namespace backend\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Goods;

/**
 * GoodsSearch represents the model behind the search form about `backend\models\Goods`.
 */
class GoodsSearch extends Goods
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'has_option', 'sales', 'status', 'sort', 'stock', 'stock_warning', 'freight_model_id', 'append', 'updated', 'is_del'], 'integer'],
            [['title', 'sub_title', 'thumb', 'thumbs', 'thumb_video', 'content', 'upc_code', 'intro', 'units', 'hot', 'associated_goods'], 'safe'],
            [['price', 'crossed_price', 'weight', 'score'], 'number'],
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
        $query = Goods::find();

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
            'category_id' => $this->category_id,
            'has_option' => $this->has_option,
            'price' => $this->price,
            'crossed_price' => $this->crossed_price,
            'sales' => $this->sales,
            'status' => $this->status,
            'sort' => $this->sort,
            'weight' => $this->weight,
            'stock' => $this->stock,
            'stock_warning' => $this->stock_warning,
            'score' => $this->score,
            'freight_model_id' => $this->freight_model_id,
            'append' => $this->append,
            'updated' => $this->updated,
            'is_del' => $this->is_del,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'sub_title', $this->sub_title])
            ->andFilterWhere(['like', 'thumb', $this->thumb])
            ->andFilterWhere(['like', 'thumbs', $this->thumbs])
            ->andFilterWhere(['like', 'thumb_video', $this->thumb_video])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'upc_code', $this->upc_code])
            ->andFilterWhere(['like', 'intro', $this->intro])
            ->andFilterWhere(['like', 'units', $this->units])
            ->andFilterWhere(['like', 'hot', $this->hot])
            ->andFilterWhere(['like', 'associated_goods', $this->associated_goods]);

        if (!empty($this->start_time)) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->start_time)]);
        }
        if (!empty($this->end_time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($this->end_time) + 24 * 3600 - 1]);
        }

        return $dataProvider;
    }
}
