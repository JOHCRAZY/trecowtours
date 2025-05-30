<?php

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base-model class for table "services".
 *
 * @property integer $service_id
 * @property string $name
 * @property string $description
 * @property string $short_description
 * @property string $base_price
 * @property string $price_per_couple
 * @property integer $duration_days
 * @property integer $duration_nights
 * @property integer $max_participants
 * @property integer $min_participants
 * @property integer $is_featured
 * @property integer $is_active
 * @property integer $service_type_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \common\models\ServiceCategories[] $categories
 * @property \common\models\ServiceBookings[] $serviceBookings
 * @property \common\models\ServiceExclusions[] $serviceExclusions
 * @property \common\models\ServiceFeatures[] $serviceFeatures
 * @property \common\models\ServiceHighlights[] $serviceHighlights
 * @property \common\models\ServiceImages[] $serviceImages
 * @property \common\models\ServiceInclusions[] $serviceInclusions
 * @property \common\models\ServiceLocations[] $serviceLocations
 * @property \common\models\ServicePricing[] $servicePricings
 * @property \common\models\ServiceToCategory[] $serviceToCategories
 * @property \common\models\LkpServiceTypes $serviceType
 */
abstract class Services extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // $behaviors['timestamp'] = [
        //     'class' => TimestampBehavior::class,
        //                 ];
        
    return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['description', 'short_description', 'duration_days', 'duration_nights', 'max_participants', 'service_type_id'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['is_featured'], 'default', 'value' => 0],
            [['name', 'base_price', 'price_per_couple'], 'required'],
            [['description'], 'string'],
            [['base_price', 'price_per_couple'], 'number'],
            [['duration_days', 'duration_nights', 'max_participants', 'min_participants', 'is_featured', 'is_active', 'service_type_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['short_description'], 'string', 'max' => 500],
            [['service_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\LkpServiceTypes::class, 'targetAttribute' => ['service_type_id' => 'service_type_id']]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'service_id' => Yii::t('app', 'Service ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'short_description' => Yii::t('app', 'Short Description'),
            'base_price' => Yii::t('app', 'Base Price'),
            'price_per_couple' => Yii::t('app', 'Price Per Couple'),
            'duration_days' => Yii::t('app', 'Duration Days'),
            'duration_nights' => Yii::t('app', 'Duration Nights'),
            'max_participants' => Yii::t('app', 'Max Participants'),
            'min_participants' => Yii::t('app', 'Min Participants'),
            'is_featured' => Yii::t('app', 'Is Featured'),
            'is_active' => Yii::t('app', 'Is Active'),
            'service_type_id' => Yii::t('app', 'Service Type ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(\common\models\ServiceCategories::class, ['category_id' => 'category_id'])->viaTable('service_to_category', ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceBookings()
    {
        return $this->hasMany(\common\models\ServiceBookings::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceExclusions()
    {
        return $this->hasMany(\common\models\ServiceExclusions::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceFeatures()
    {
        return $this->hasMany(\common\models\ServiceFeatures::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceHighlights()
    {
        return $this->hasMany(\common\models\ServiceHighlights::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceImages()
    {
        return $this->hasMany(\common\models\ServiceImages::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceInclusions()
    {
        return $this->hasMany(\common\models\ServiceInclusions::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceLocations()
    {
        return $this->hasMany(\common\models\ServiceLocations::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePricings()
    {
        return $this->hasMany(\common\models\ServicePricing::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceToCategories()
    {
        return $this->hasMany(\common\models\ServiceToCategory::class, ['service_id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceType()
    {
        return $this->hasOne(\common\models\LkpServiceTypes::class, ['service_type_id' => 'service_type_id']);
    }

}
