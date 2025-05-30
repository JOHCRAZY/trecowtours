<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Services;

/**
* ServiceSearch represents the model behind the search form about `common\models\Services`.
*/
class ServiceSearch extends Services
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['service_id', 'duration_days', 'duration_nights', 'max_participants', 'min_participants', 'is_featured', 'is_active', 'service_type_id'], 'integer'],
            [['name', 'description', 'short_description', 'created_at', 'updated_at'], 'safe'],
            [['base_price', 'price_per_couple'], 'number'],
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
$query = Services::find();



$dataProvider = new ActiveDataProvider([
'query' => $query,
]);

$this->load($params);

if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
return $dataProvider;
}

$query->andFilterWhere([
            'service_id' => $this->service_id,
            'base_price' => $this->base_price,
            'price_per_couple' => $this->price_per_couple,
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'max_participants' => $this->max_participants,
            'min_participants' => $this->min_participants,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'service_type_id' => $this->service_type_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'short_description', $this->short_description]);

return $dataProvider;
}
}
