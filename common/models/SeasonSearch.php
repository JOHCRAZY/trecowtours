<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LkpSeasons;

/**
* SeasonSearch represents the model behind the search form about `common\models\LkpSeasons`.
*/
class SeasonSearch extends LkpSeasons
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['season_id', 'start_month', 'end_month', 'is_active'], 'integer'],
            [['season_name', 'description'], 'safe'],
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
$query = LkpSeasons::find();



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
            'season_id' => $this->season_id,
            'start_month' => $this->start_month,
            'end_month' => $this->end_month,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'season_name', $this->season_name])
            ->andFilterWhere(['like', 'description', $this->description]);

return $dataProvider;
}
}
