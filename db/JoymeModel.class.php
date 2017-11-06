<?php

/**
 * JOYME Model模型类
 * 实现了ORM和ActiveRecords模式
 *
 * @author kexuedong <dkxnight@gmail.com>
 * @date 2015-05-14 10:14:49
 * @copyright joyme
 */

namespace Joyme\db;

use Joyme\db\JoymeDb as Db;
use Joyme\core\JoymeComm;

class JoymeModel extends JoymeComm {

    // 当前数据库操作对象
    public $db = null;
    //数据库配置
    protected $db_config = array();
    // 数据表名
    public $tableName = '';
    // 字段信息
    protected $fields = array();

    /**
     * 架构函数
     * @access public
     */
    public function __construct() {
        // 创建一个新的实例
        $this->db = new Db($this->db_config, $this->tableName);
    }

    /**
     * 新增数据
     * @access public
     * @param mixed $data 数据
     * @param boolean $replace 是否replace
     * @return mixed
     */
    public function insert($data = array()) {
        if ($this->connect()) {
            if (empty($data)) {
                return false;
            }
            // 写入数据到数据库
            return $this->db->insert($data);
        }
        return false;
    }

    /**
     * 保存数据
     * @access public
     * @param mixed $data 数据
     * @param array $options 表达式
     * @return boolean
     */
    public function update($data = array(), $where = array()) {

        if ($this->connect()) {
            if (empty($data)) {
                return false;
            }
            return $this->db->update($data, $where);
        }
        return false;
    }

    /**
     * 删除数据
     * @access public
     * @param mixed $where 表达式
     * @return mixed
     */
    public function delete($where = array()) {
        if ($this->connect()) {
            return $this->db->delete($where);
        }
        return false;
    }

    /**
     * 查询数据集
     * @access public
     * @param $fields 字段
     * @param $where 条件判断
     * @param $order 排序
     * @param $limit 限制条数
     * @param $skip
     * @param $group 分组
     * @return mixed
     */
    public function select($fields = '*', $where = array(), $order = '', $limit = 20, $skip = 0, $group = '') {
        if ($this->connect()) {
            return $this->db->select($fields, $where, $order, $limit, $skip, $group);
        }
        return false;
    }

    /**
     * 查询单条数据
     * @param $fields 字段
     * @param $where 条件判断
     * @param $order 排序
     * @param $group 分组
     * @return array $resultSet[0]
     */
    public function selectRow($fields = '*', $where = array(), $order = '', $group = '') {
        $limit = 1;
        $resultSet = $this->select($fields, $where, $order, $limit, $skip = '', $group);
        if ($resultSet)
            return $resultSet[0];
        else
            return null;
    }

    /**
     * 返回 按条件 count 的数量
     * @param array $where
     * @return int
     */
    public function count($where = array()) {
        if ($this->connect()) {
            $sql = "select count(1) as count from  " . $this->tableName . "  " . $this->db->parseWhere($where) . ";";
            $ret = $this->excuteSql($sql);
            if (empty($ret)) {
                return 0;
            }
            return (int) $ret[0]['count'];
        }
        return false;
    }

    /**
     * 设置int类型自增自减
     * @param string $field
     * @param array $where
     * @param default string $step=1
     * @return mixed
     */
    public function numChange($field, $where, $step = 1) {
        if (!$field || !$where) {
            return false;
        }
        //保证有处理！
        $field = $this->db->escapeString($field);
        $step = intval($step);
        if ($this->connect()) {
            $sql = "UPDATE " . $this->tableName . " SET " . $field . " = " . $field . " + " . $step . $this->db->parseWhere($where);
            return $this->db->excuteSql($sql);
        }
        return false;
    }

    /**
     * SQL查询
     * @access public
     * @param string $sql SQL指令
     * @return mixed
     */
    public function excuteSql($sql) {
        if ($this->connect()) {
            return $this->db->excuteSql($sql);
        }
        return false;
    }

    /**
     * 数据库连接
     * @access public
     * @param mixed $config 数据库连接信息
     * @return Model
     */
    private function connect() {
        if ($this->db) {
            //do nth
        } else {
            $this->db = new Db($this->db_config, $this->tableName);
        }
        return $this->db->ping();
    }

    /**
     * 获取当前查询sql语句
     * @access public
     * @return string
     */
    public function getQuerySql() {
        return $this->db->getQuerySql();
    }

    /**
     * 得到数据表名
     * @access public
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * 得到字段
     * @access public
     * $return array
     */
    public function getDbFields() {
        return $this->fields;
    }

    /**
     * 获取数据表字段信息
     * @access public
     * @return array
     */
    public function getDbFieldsByDB() {
        if ($this->connect()) {
            if ($this->getTableName()) {
                $fields = $this->db->getFields($this->getTableName());
                return $fields ? array_keys($fields) : false;
            }
        }
        return false;
    }

}
