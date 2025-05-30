<?php
namespace common\widgets;

use yii\base\Widget;
use common\assets\NotificationAsset;

class NotificationWidget extends Widget
{
    public $message;
    public $type = 'info'; // info, success, error, warning
    public $description;
    public $buttonText = 'OK';
    
    public function init()
    {
        parent::init();
        // Register asset bundle for CSS and JS.
        NotificationAsset::register($this->getView());
        ob_start();
    }
    
    public function run()
    {
        $content = ob_get_clean();
        if (empty($this->message)) {
            $this->message = $content;
        }
        
        return $this->render('widgets/notification', [
            'message'    => $this->message,
            'description'=> $this->description,
            'type'       => $this->type,
            'buttonText' => $this->buttonText
        ]);
    }
}
