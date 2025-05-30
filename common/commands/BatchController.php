<?php 
namespace common\commands;

use yii\console\Controller;
use schmunk42\giiant\commands\BatchController as GiiantBatchController;

class BatchController extends GiiantBatchController
{
    // Base path for generating models
    public $modelNamespace = 'common\\models';
    public $modelQueryNamespace = 'common\\models\\query';
    
    // Base path for generating CRUD
    public $crudControllerNamespace = 'common\\controllers';
    public $crudSearchModelNamespace = 'common\\models\\search';
    //public $crudViewPath = '@app/views';
    
    // Customize model generation as needed
    public $modelBaseClass = 'yii\\db\\ActiveRecord';
    public $modelMessageCategory = 'app';
    
    // Skip these tables if needed
    public $tableIgnoreList = [
        'migration',
        '',
    ];
}