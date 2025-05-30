<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Subscribers;

/**
 * SubscribersSearch represents the model behind the search form of `common\models\Subscribers`.
 */
class SubscribersSearch extends Subscribers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id'], 'integer'],
            [['email', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Subscribers::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 10,
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
            'subscriber_id' => $this->subscriber_id,
        ]);

        if ($this->created_at) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at . ' 00:00:00'])
                  ->andFilterWhere(['<=', 'created_at', $this->created_at . ' 23:59:59']);
        }

        $query->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}