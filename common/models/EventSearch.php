<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Event;

/**
* EventSearch represents the model behind the search form about `common\models\Event`.
*/
class EventSearch extends Event
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['event_id', 'tour_id'], 'integer'],
            [['event_name', 'description', 'event_date', 'start_time', 'end_time', 'location', 'created_at', 'updated_at'], 'safe'],
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
$query = Event::find();



$dataProvider = new ActiveDataProvider([
'query' => $query,
'sort' => [
'defaultOrder' => [
'created_at' => SORT_DESC,
],
],
]);

$this->load($params);

if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
return $dataProvider;
}

$query->andFilterWhere([
            'event_id' => $this->event_id,
            'tour_id' => $this->tour_id,
            'event_date' => $this->event_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'event_name', $this->event_name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'location', $this->location]);

return $dataProvider;
}
}
