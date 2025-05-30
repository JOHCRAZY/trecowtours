<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Contact;

/**
* ContactSearch represents the model behind the search form about `common\models\Contact`.
*/
class ContactSearch extends Contact
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['contact_id', 'is_active'], 'integer'],
            [['platform', 'contact_value', 'contact_type'], 'safe'],
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
$query = Contact::find();



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
            'contact_id' => $this->contact_id,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'platform', $this->platform])
            ->andFilterWhere(['like', 'contact_value', $this->contact_value])
            ->andFilterWhere(['like', 'contact_type', $this->contact_type]);

return $dataProvider;
}
}
