<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "service_categories".
 *
 * @property integer $category_id
 * @property string $name
 * @property string $description
 * @property integer $is_active
 *
 * @property \common\models\ServiceToCategory[] $serviceToCategories
 * @property \common\models\Services[] $services
 */
abstract class ServiceCategories extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_categories';
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
            [['name'], 'required'],
            [['description'], 'string'],
            [['is_active'], 'integer'],
            [['name'], 'string', 'max' => 50]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'category_id' => Yii::t('app', 'Category ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'is_active' => Yii::t('app', 'Is Active'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceToCategories()
    {
        return $this->hasMany(\common\models\ServiceToCategory::class, ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(\common\models\Services::class, ['service_id' => 'service_id'])->viaTable('service_to_category', ['category_id' => 'category_id']);
    }

}
