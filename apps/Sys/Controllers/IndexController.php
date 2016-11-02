<?php
namespace Apps\Sys\Controllers;

use Apps\Sys\Entity\Module;
use Common\Controller;
use Wedo\Dispatcher;
use Common\Models\ModuleModel;

class IndexController extends Controller {
    public function indexAction() {
        echo Dispatcher::getInstance()->getControllerClass(Dispatcher::getInstance()->getRequest());
        // $this->display();
        $module = ModuleModel::instance()->get('sys')->entity();
        var_dump($module);
        // add module
        $new_module = new Module();
        $new_module->setModule('adm');
        $new_module->setName('Test');
        $new_module->setVersion('1.0');
        $new_module->setDescription('行政相关业务');
        $new_module->setDisplayOrder(80);
//        ModuleModel::instance()->addEntity($new_module);
//        ModuleModel::instance()->updateEntity($new_module);
        echo '<br><br>';
        $cond = new Module();
        $cond->setModule(array('sys', 'adm'), 'IN');
        $modules = ModuleModel::instance()->getAll($cond)->entityResult();
        var_dump($modules);
    }
}