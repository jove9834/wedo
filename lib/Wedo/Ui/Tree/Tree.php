<?php
/**
 * Tree组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Tree;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;
use Wedo\Ui\Expression;
use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Filesystem;
use Wedo\View\PhpEngine;
use Wedo\Dispatcher;
use Wedo\View\BladeCompiler;

/**
 * Tree组件
 */
class Tree extends Container {
    /**
     * 组件ID
     *
     * @var string
     */
    protected $id;    
    
    /**
     * 数据源
     *
     * @var string|array
     */
    protected $ds;

    /**
     * 设置ID
     *
     * @param mixed $value 值
     * @return void
     */
    public function setId($value) {
        $this->id = $value;
    }

    /**
     * 获取ID
     *
     * @return void
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { 
        parent::init();
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        // 缓存
        $this->cacheTemplate('treefolder');
        $this->cacheTemplate('treeitem');
        $ds_var = NULL;
        if ($this->ds instanceof Expression) {
            $ds_var = $this->ds->getContent();
        } elseif (is_array($this->ds)) {
            $ds_var = '$_ds';
            $this->addVar('$_ds', $this->ds);    
        }
        
        $code = wd_print('<?php echo Wedo\Ui\Tree\Tree::renderTree("{}", {});?>', $this->id, $ds_var) . PHP_EOL;
        
        return $code;
    }

    /**
     * 树目录结点脚本
     *
     * @return string
     */
    public function cacheTemplate($type) {
        $children = $this->getChildren($type);
        if ($children) {
            $code = $children[0]->getContent();
            $compiler = new BladeCompiler();
            $content = $compiler->compileString($code);
            $fs = new Filesystem();
            $fs->put(self::getCacheFile($this->getId(), $type), $content);
        }
    }

    /**
     * 递归树
     *
     * @return void
     */
    public static function renderTree($treeId, $data) {
        $html = "";
        $count = count($data);
        $engine = new PhpEngine();
        
        foreach ($data as $index => $item) {
            $children = wd_array_val($item, 'children');
            $type = wd_array_val($item, 'type');
            if ($type == 'folder') {
                $template_file = self::getCacheFile($treeId, 'treefolder');
            }
            else {
                $template_file = self::getCacheFile($treeId, 'treeitem');
            }
            
            $item['_index_'] = $index;
            $item['_count_'] = $count;

            $sub_html = $engine->evaluatePath($template_file, $item);
            // 取子结点的HTML
            if ($children && is_array($children)) {
                $children_html = self::renderTree($treeId, $children);
            }
            else {
                $children_html = '';
            }

            // 替换标签
            $sub_html = str_replace('<wd:children/>', $children_html, $sub_html);
            
            $html .= $sub_html;
        }
    
        return $html;
    }

    /**
     * 获取缓存文件名
     *
     * @return string
     */
    public static function getCacheFile($id, $type) {
        $file = $id . '_' . $type;
        $cache_file = DATA_PATH . '/views/' . $file;
        return $cache_file;
    }
}