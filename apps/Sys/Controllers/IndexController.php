<?php
namespace Apps\Sys\Controllers;

use Common\Components\Module;
use Common\Controller;
use Wedo\Dispatcher;
use Common\Models\ModuleModel;

class IndexController extends Controller {
    public function get() {
//        echo Dispatcher::instance()->getControllerClass(Dispatcher::instance()->getRequest());
//        echo 'test';
        echo json_encode(array('a' => 1, 'b' => 'test'));
        // $this->display();
//        $module = ModuleModel::instance()->get('sys')->entity();
//        var_dump($module);
        // add module
//        $new_module = new Module();
//        $new_module->setModule('adm');
//        $new_module->setName('Test');
//        $new_module->setVersion('1.0');
//        $new_module->setDescription('行政相关业务');
//        $new_module->setDisplayOrder(80);
////        ModuleModel::instance()->addEntity($new_module);
////        ModuleModel::instance()->updateEntity($new_module);
//        echo '<br><br>';
//        $cond = new Module();
//        $cond->setModule(array('sys', 'adm'), 'IN');
//        $modules = ModuleModel::instance()->getAll($cond)->entityResult();
//        var_dump($modules);
    }

    public function post() {
        echo 'post';
    }

    public function postUser($uid) {
        echo 'test-' . $uid;
    }

    public function put() {
        echo 'put';
    }

    public function delete() {
        echo 'delete';
    }
}