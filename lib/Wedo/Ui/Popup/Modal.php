<?php
/**
 * Modal 组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Popup;

use Wedo\Ui\Container;

/**
 * Modal 组件
 */ 
class Modal extends Container {
    /**
     * ID
     *
     * @var string
     */
    protected $id;

    /**
     * 标题
     *
     * @var string
     */
    protected $title;

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
        $html = '<div class="modal fade" id="{}" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                </button>
                                <h4 class="modal-title">
                                    {}
                                </h4>
                            </div>
                            <div class="modal-body no-padding">
                            {}
                            </div>
                        </div>
                    </div>
                </div>';
        $html = wd_print($html, $this->id, $this->title, $this->getContent());
        return $html;
    }
}