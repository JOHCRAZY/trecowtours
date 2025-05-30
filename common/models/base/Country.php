<?php

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "country".
 *
 * @property integer $id
 * @property string $iso
 * @property string $name
 * @property string $nicename
 * @property string $iso3
 * @property integer $numcode
 * @property integer $phonecode
 */
abstract class Country extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['iso3', 'numcode'], 'default', 'value' => null],
            [['iso', 'name', 'nicename', 'phonecode'], 'required'],
            [['numcode', 'phonecode'], 'integer'],
            [['iso'], 'string', 'max' => 2],
            [['name', 'nicename'], 'string', 'max' => 80],
            [['iso3'], 'string', 'max' => 3]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => Yii::t('app', 'ID'),
            'iso' => Yii::t('app', 'Iso'),
            'name' => Yii::t('app', 'Name'),
            'nicename' => Yii::t('app', 'Nicename'),
            'iso3' => Yii::t('app', 'Iso3'),
            'numcode' => Yii::t('app', 'Numcode'),
            'phonecode' => Yii::t('app', 'Phonecode'),
        ]);
    }

}
