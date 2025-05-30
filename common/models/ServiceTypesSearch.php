<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LkpServiceTypes;

/**
* ServiceTypesSearch represents the model behind the search form about `common\models\LkpServiceTypes`.
*/
class ServiceTypesSearch extends LkpServiceTypes
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['service_type_id', 'is_active'], 'integer'],
            [['type_name', 'description'], 'safe'],
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
$query = LkpServiceTypes::find();



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
            'service_type_id' => $this->service_type_id,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'type_name', $this->type_name])
            ->andFilterWhere(['like', 'description', $this->description]);

return $dataProvider;
}
}
