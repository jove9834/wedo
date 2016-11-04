<?php
/**
 * 生成实体类和数据模型类
 *
 * @author     黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link       http://weidu178.com
 * @since      Version 1.0
 */
namespace Apps\Sys\Controllers;

use Common\Controller;
use Wedo\Dispatcher;
use Wedo\Config;
use Wedo\Filesystem;
use \Exception;
use Wedo\Database\Database;

class GenController extends Controller {
    /**
     * 生成文件
     * URL: http://localhost/framework/public/sys/gen/index?table=sys_db&pre=sys_&ns=Apps\Sys
     * table: 表名，可以多个表，以逗号分隔
     * pre： 表名前缀
     * ns: 生成的类命名空间
     */
    public function indexAction() {
        $table = wd_input('table');
        $ns = wd_input('ns');
        $conn = wd_input('conn');
        $pre = wd_input('pre');

        if (!$table || !$ns) {
            echo '参数不正确，格式如：http://localhost/framework/public/sys/gen/index?table=sys_db&pre=sys_&ns=Apps\Sys';
            return;
        }

        $tableList = explode(',', $table);
        foreach ($tableList as $table) {
            $this->genFile($table, $ns, $conn, $pre);
        }
    }

    private function genFile($table, $ns, $conn, $pre) {
        $table = trim($table);
        if (! $pre) {
            $name = $table;
        } else {
            $name = substr($table, strlen($pre));
        }

        $entityName = ucfirst(wd_camel_case($name));
        list($db, $schema) = $this->connection($conn);
        $sql = "select * from information_schema.COLUMNS where table_name = '{$table}' and table_schema = '{$schema}'";
        $columns = $db->getAll($sql);

        $fileds = array();
        $primaryKey = array();
        if ($columns) {
            foreach ($columns as $col) {
                $name = $col['COLUMN_NAME'];
                $columnKey = $col['COLUMN_KEY'];
                $fileds[] = wd_camel_case($name);
                if ($columnKey == 'PRI') {
                    $primaryKey[] = $name;
                }
            }
        }

        if (count($primaryKey) > 1) {
            $pk = wd_print('array({})', implode(',', array_map(function($n) { return "'" . $n . "'";}, $primaryKey)));
        } else {
            $pk = $primaryKey ? "'" . array_shift($primaryKey) . "'": NULL;
        }

        $entityNs = $ns . "\\Entity";
        $vars = array('tableName' => $table, 'entityName' => $entityName, 'conn' => $conn, 'ns' => $entityNs, 'pk' => $pk, 'fields' => $fileds);
        $entityContent = Dispatcher::instance()->getView()->render('Apps\Sys::gen.entity', $vars);
        $entityFile = $this->save($entityName, $entityNs, $entityContent);
        // 生成模型文件
        $modelNs = $ns . "\\Models";
        $vars = array('tableName' => $table, 'entityName' => $entityName, 'conn' => $conn, 'ns' => $ns, 'pk' => $pk, 'fields' => $fileds);
        $content = Dispatcher::instance()->getView()->render('Apps\Sys::gen.model', $vars);
        $modelFile = $this->save($entityName . 'Model', $modelNs, $content);
        echo wd_print('Entity file:{} <br>Model file: {}<br>', $entityFile, $modelFile);
    }

    /**
     * 保存文件
     * @param string $fileName 文件名
     * @param string $ns 命名空间
     * @param string $content 文件内容
     * @return string 返回文件名
     * @throws Exception
     */
    private function save($fileName, $ns, $content) {
        if (! $content) {
            throw new Exception(wd_print('{} 文件内容为空', $fileName));
        }

        $content = '<?php' . PHP_EOL . $content;
        $files = new Filesystem();
        $path = DATA_PATH . DIRECTORY_SEPARATOR . 'gen' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $ns) . DIRECTORY_SEPARATOR;
        $files->makeDirectory($path, 0755, TRUE, TRUE);
        $file = $path . $fileName . '.php';
        $files->put($file, $content);
        return $file;
    }

    /**
     * 数据连接
     *
     * @param string $connection 连接名
     * @throws Exception
     * @return Database
     */
    private function connection($connection = NULL) {
        $config = Config::load('database', NULL, TRUE);
        if (! $config) {
            throw new Exception('没有找到数据库配置信息在config/database.php');
        }

        if ($connection) {
            $database = wd_array_val($config, $connection);
            if (! $database) {
                throw new Exception('数据库连接配置 ' . $connection . ' 不存在！');
            }
        }
        else {
            $database = current($config);
        }

        $debug = wd_array_val($database, 'debug', FALSE);

        $conn = new Database($database['host'], $database['username'], $database['password'], $database['database']);
        $conn->setCharset($database['charset']);
        $this->prefix = wd_array_val($database, 'prefix');
        $debug && $conn->setDebug();
        return array($conn, $database['database']);
    }

}