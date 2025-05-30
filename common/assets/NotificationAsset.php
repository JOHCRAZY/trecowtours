<?php
namespace common\assets;

use yii\web\AssetBundle;

class NotificationAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/notification';
    public $css = [
        'css/notification.css',
    ];
    public $js = [];
    public $publishOptions = [
        'forceCopy' => true,
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
