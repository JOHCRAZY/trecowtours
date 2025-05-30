<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tour;

/**
* TourSearch represents the model behind the search form about `common\models\Tour`.
*/
class TourSearch extends Tour
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['tour_id', 'duration_days', 'duration_nights', 'total_seats', 'available_seats', 'is_deleted'], 'integer'],
            [['tour_name', 'description', 'start_date', 'end_date', 'booking_deadline', 'starting_point', 'status', 'created_at', 'updated_at'], 'safe'],
            [['single_price', 'couple_price'], 'number'],
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
$query = Tour::find();



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
            'tour_id' => $this->tour_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'booking_deadline' => $this->booking_deadline,
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'single_price' => $this->single_price,
            'couple_price' => $this->couple_price,
            'total_seats' => $this->total_seats,
            'available_seats' => $this->available_seats,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'tour_name', $this->tour_name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'starting_point', $this->starting_point])
            ->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}
