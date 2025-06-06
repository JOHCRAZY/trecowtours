<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;
 
/**
 * This is the base-model class for table "service_features".
 *
 * @property integer $feature_id
 * @property integer $service_id
 * @property string $feature
 * @property string $description
 * @property integer $display_order
 *
 * @property \common\models\Services $service
 */
abstract class ServiceFeatures extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_features';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['description'], 'default', 'value' => null],
            [['display_order'], 'default', 'value' => 0],
            [['service_id', 'feature'], 'required'],
            [['service_id', 'display_order'], 'integer'],
            [['description'], 'string'],
            [['feature'], 'string', 'max' => 255],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Services::class, 'targetAttribute' => ['service_id' => 'service_id']]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'feature_id' => Yii::t('app', 'Feature ID'),
            'service_id' => Yii::t('app', 'Service ID'),
            'feature' => Yii::t('app', 'Feature'),
            'description' => Yii::t('app', 'Description'),
            'display_order' => Yii::t('app', 'Display Order'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(\common\models\Services::class, ['service_id' => 'service_id']);
    }

}
