<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "service_locations".
 *
 * @property integer $service_id
 * @property integer $location_id
 * @property integer $is_start_point
 * @property integer $is_end_point
 * @property integer $visit_order
 * @property string $duration_hours
 * 
 * @property \common\models\Locations $location
 * @property \common\models\Services $service
 */
abstract class ServiceLocations extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_locations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['duration_hours'], 'default', 'value' => null],
            [['is_end_point'], 'default', 'value' => 0],
            [['service_id', 'location_id', 'visit_order'], 'required'],
            [['service_id', 'location_id', 'is_start_point', 'is_end_point', 'visit_order'], 'integer'],
            [['duration_hours'], 'number'],
            [['service_id', 'location_id', 'visit_order'], 'unique', 'targetAttribute' => ['service_id', 'location_id', 'visit_order']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Locations::class, 'targetAttribute' => ['location_id' => 'location_id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Services::class, 'targetAttribute' => ['service_id' => 'service_id']]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'service_id' => Yii::t('app', 'Service ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'is_start_point' => Yii::t('app', 'Is Start Point'),
            'is_end_point' => Yii::t('app', 'Is End Point'),
            'visit_order' => Yii::t('app', 'Visit Order'),
            'duration_hours' => Yii::t('app', 'Duration Hours'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(\common\models\Locations::class, ['location_id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(\common\models\Services::class, ['service_id' => 'service_id']);
    }

}
