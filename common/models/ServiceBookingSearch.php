<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceBookings;

/**
* ServiceBookingSearch represents the model behind the search form about `common\models\ServiceBookings`.
*/
class ServiceBookingSearch extends ServiceBookings
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['booking_id', 'service_id', 'number_of_people'], 'integer'],
            [['booking_reference', 'customer_name', 'customer_email', 'customer_phone', 'booking_date', 'tour_date', 'end_date', 'special_requests', 'status', 'created_at', 'updated_at'], 'safe'],
            [['total_price'], 'number'],
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
$query = ServiceBookings::find();



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
            'booking_id' => $this->booking_id,
            'service_id' => $this->service_id,
            'number_of_people' => $this->number_of_people,
            'booking_date' => $this->booking_date,
            'tour_date' => $this->tour_date,
            'end_date' => $this->end_date,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'booking_reference', $this->booking_reference])
            ->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_email', $this->customer_email])
            ->andFilterWhere(['like', 'customer_phone', $this->customer_phone])
            ->andFilterWhere(['like', 'special_requests', $this->special_requests])
            ->andFilterWhere(['like', 'status', $this->status]);

return $dataProvider;
}
}
