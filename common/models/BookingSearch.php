<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Booking;

/**
 * BookingSearch represents the model behind the search form about `common\models\Booking`.
 */
class BookingSearch extends Booking
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['booking_id', 'tour_id', 'customer_id', 'is_deleted'], 'integer'],
            [['booking_type', 'booking_date', 'payment_status'], 'safe'],
            [['total_amount', 'discount_applied'], 'number'],
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
        $query = Booking::find();



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                    'defaultOrder' => [
                        'booking_date' => SORT_DESC,
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
            'booking_id' => $this->booking_id,
            'tour_id' => $this->tour_id,
            'customer_id' => $this->customer_id,
            'total_amount' => $this->total_amount,
            'discount_applied' => $this->discount_applied,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'booking_type', $this->booking_type])
            ->andFilterWhere(['like', 'booking_date', $this->booking_date])
            ->andFilterWhere(['like', 'booking_status', $this->booking_status])
            ->andFilterWhere(['like', 'payment_status', $this->payment_status]);

        return $dataProvider;
    }
}
