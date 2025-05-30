<?php

namespace common\models;

use \common\models\base\Services as BaseService;

/**
 * This is the model class for table "services".
 */
class Services extends BaseService
{
    public $discount_percentage;
    public $discounted_price;
    public $rating;

    public $location;
 
}
