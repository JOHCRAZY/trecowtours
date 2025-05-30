<?php

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "lkp_seasons".
 *
 * @property integer $season_id
 * @property string $season_name
 * @property integer $start_month
 * @property integer $end_month
 * @property string $description
 * @property integer $is_active
 *
 * @property \common\models\ServicePricing[] $servicePricings
 */
abstract class LkpSeasons extends \yii\db\ActiveRecord
{

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lkp_seasons';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['description'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['season_name', 'start_month', 'end_month'], 'required'],
            [['start_month', 'end_month', 'is_active'], 'integer'],
            [['description'], 'string'],
            [['season_name'], 'string', 'max' => 30],
            [['season_name'], 'unique']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'season_id' => Yii::t('app', 'Season ID'),
            'season_name' => Yii::t('app', 'Season Name'),
            'start_month' => Yii::t('app', 'Start Month'),
            'end_month' => Yii::t('app', 'End Month'),
            'description' => Yii::t('app', 'Description'),
            'is_active' => Yii::t('app', 'Is Active'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePricings()
    {
        return $this->hasMany(\common\models\ServicePricing::class, ['season_id' => 'season_id']);
    }

}
