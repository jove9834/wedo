<?php
/**
 * 表达式
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

/**
 * 表达式
 */
class Expression {
    const TYPE_EXPRESSION = 1;
    const TYPE_STRING = 2;
    const TYPE_JSON = 3;
    const TYPE_ARRAY = 4;
    const TYPE_BOOL = 5;
    
    /**
     * 变量名称
     *
     * @var string
     */
    private $varname;   

    /**
     * 表达式内容
     *
     * @var string
     */
    private $content;

    /**
     * 类型
     *
     * @var string
     */
    private $type;

    /**
     * 构造函数
     *
     * @param string $type    表达式类型
     * @param string $content 表达式内容
     * @param string $varname 变量名称     
     */
    public function __construct($type, $content, $varname = NULL) {
        $this->varname = $varname;
        $this->type = $type;
        $this->content = $content;
    }


    /**
     * 判断字符串是否是表达式
     *
     * @param string $s 字符串
     * @return boolean
     */
    public static function isExpression($s) {
        $s = trim($s);
        return starts_with($s, '{{') && ends_with($s, '}}');
    }

    /**
     * 获取字符串类型
     *
     * @param mixed $s 字符串
     * @return integer
     */
    public static function getExpressionType($s) {
        if (is_array($s)) {
            return self::TYPE_ARRAY;
        } elseif (self::isExpression($s)) {
            return self::TYPE_EXPRESSION;
        } elseif (wd_is_json($s)) {
            return self::TYPE_JSON;
        } elseif (is_bool($s) || (strtolower(trim($s)) == 'true' || strtolower(trim($s)) == 'false')) {
            return self::TYPE_BOOL;
        } else {            
            return self::TYPE_STRING;
        }
    }

    /**
     * 解析字符串
     *
     * @param string $s 字符串
     * @return Expression
     */
    public static function parse($s) {
        $type = self::getExpressionType($s);
        switch ($type) {
            case self::TYPE_EXPRESSION:
                $content = substr($s, 2, -2);
                return new Expression($type, $content);                
            case self::TYPE_ARRAY:
                return $s;
            case self::TYPE_JSON:
                return wd_json_decode($s);
            case self::TYPE_BOOL:
                return is_bool($s) ? $s : (strtolower(trim($s)) == 'true');            
        }

        return $s;
    }

    /**
     * 取标签表达式
     *
     * @param mixed  $expr    表达式
     * @return string
     */
    public static function getTagExpression($expr) {
        if ($expr instanceof Expression) {
            return '{{' . $expr->getContent() . '}}';
        } else if (is_bool($expr)) {
            return $expr ? 'true' : 'false';
        } else if (is_array($expr)) {
            return $expr ? 'true' : 'false';
        } else {
            return htmlspecialchars($expr);
        }        
    }

    /**
     * 获取表达式字符串,即用于代码中的表达式
     *
     * @param mixed  $expr    表达式
     * @return string
     */
    public static function getExpressionString($expr, $varname = NULL) {
        if ($varname) {
            return $varname;
        } else if ($expr instanceof Expression) {
            return $expr->content;
        } else if (is_array($expr)) {
            return var_export($expr, TRUE);
        } else if (is_bool($expr)) {
            return $expr ? 'true' : 'false';
        } else {
            return '"' . str_replace('"', '\"', $expr) . '"';
        }
    }

    /**
     * 取表达式的PHP代码
     *
     * @param mixed  $expr    表达式
     * @param string $varname 变量名
     * @return string
     */
    public static function getPhpcode($expr, $varname = NULL) {
        if (! $varname) {
            if (! $expr) {
                return $expr;
            }
            else if ($expr instanceof Expression) {
                return '<?php echo ' . $expr->getContent() . '; ?>';
            }
            else if (is_array($expr)) {
                return '<?php echo ' . var_export($expr, TRUE) . '; ?>';
            }
            else if (is_bool($expr)) {
                return '<?php echo "' . ($expr ? 'true' : 'false') . '"; ?>';
            }
            else {
                return '<?php echo "' . str_replace('"', '\"', $expr) . '"; ?>';
            }
        }

        if (! $expr) {
            return '<?php ' . $varname . ' = NULL; ?>';
        }
        else if ($expr instanceof Expression) {
            return '<?php ' . $varname . ' = ' . $expr->getContent() . '; ?>';
        }
        else if (is_array($expr)) {
            return '<?php ' . $varname . ' = ' . var_export($expr, TRUE) . '; ?>';
        }
        else if (is_bool($expr)) {
            return '<?php ' . $varname . ' = ' . ($expr ? 'true' : 'false') . '; ?>';
        }
        else {
            return '<?php ' . $varname . ' = "' . str_replace('"', '\"', $expr) . '"; ?>';
        }
    }
   
    /**
     * 获取表达式内容
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * 变量名
     *
     * @return string
     */
    public function getVarname() {
        return $this->varname;
    }

    /**
     * 设置变量名
     *
     * @param string $varname 变量名
     * @return void
     */
    public function setVarname($varname) {
        $this->varname = $varname;
    }

    /**
     * 表达式类型
     *
     * @return integer
     */
    public function getType() {
        return $this->type;
    }

    /**
     * 取布尔值
     *
     * @return boolean
     */
    public function getBooleanValue() {
        if ($this->type == self::TYPE_BOOL) {
            return $this->content;
        }

        return FALSE;
    }
   
    /**
     * toString
     *
     * @return string
     */
    public function __toString() {
        if ($this->varname) {
            return $this->varname;
        } else if ($this->type == self::TYPE_EXPRESSION) {
            return $this->content;
        } else if ($this->type == self::TYPE_ARRAY || $this->type == self::TYPE_JSON) {
            return json_encode($this->content);
        } else if ($this->type == self::TYPE_BOOL) {
            return $this->content;
        }

        return $this->content;
    }

}