<?php

/**
 * 数据库中间层实现类
 *
 * @author kexuedong <dkxnight@gmail.com>
 * @date 2015-05-14 10:14:49
 * @copyright joyme
 */

namespace Joyme\db;

use Exception as Exception;
use Joyme\core\JoymeComm;
use Joyme\core\Log;

class JoymeDb extends JoymeComm {

    //数据库表名称
    protected $table = null;
    // 当前SQL指令
    protected $queryStr = '';
    // 当前连接对象
    protected $dbconn = null;
    // 数据库连接参数配置
    protected $config = '';
    //数据库默认字符
    protected $charset = 'utf8';
    // 数据库表达式
    protected $comparison = array('eq' => '=', 'neq' => '<>', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'notlike' => 'NOT LIKE', 'like' => 'LIKE', 'in' => 'IN', 'notin' => 'NOT IN');
    // 查询表达式
    protected $selectSql = 'SELECT %FIELD% FROM %TABLE%%WHERE%%GROUP%%ORDER%%LIMIT%';

    /**
     * 架构函数 读取数据库配置信息
     * @access public
     * @param array $config 数据库配置数组
     */
    public function __construct($config, $tableName) {
        if (!extension_loaded('mysqli')) {
            return $this->setErrMessage(new Exception('系统不支持:mysqli'));
        }
        if (!empty($config)) {
            $this->connect($config);
        }
        $this->table = $tableName;
    }

    /**
     * 数据库连接
     * @access protected
     * @param config
     * @return
     */
    private function connect($config) {
        if (!isset($this->dbconn)) {
            // 读取数据库配置
            $config = $this->parseConfig($config);
            if (empty($config))
                return false;

            $this->dbconn = mysqli_init();
            if (!$this->dbconn) {
                return $this->setErrMessage(new Exception('mysqli_init failed'));
            }

            if (!$this->dbconn->options(MYSQLI_INIT_COMMAND, "SET NAMES '" . $this->charset . "'")) {
                return $this->setErrMessage(new Exception("SET NAMES '" . $this->charset . "'"));
            }
            if (!$this->dbconn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3)) {
                return $this->setErrMessage(new Exception('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed'));
            }
            if (!$this->dbconn->real_connect($config['hostname'], $config['username'], $config['password'], $config['database'])) {
                return $this->setErrMessage(new Exception('Connect Error :' . mysqli_connect_errno()));
            }
            //modify
//              $this->dbconn = @mysqli_connect($config['hostname'], $config['username'], $config['password'], $config['database']);
//              if (mysqli_connect_errno())
//                  return $this->setErrMessage(new Exception(mysqli_connect_error()));
//              // 设置数据库编码
//              $this->dbconn->query("SET NAMES '" . $this->charset . "'");

            $this->config = $config;
        }
        return $this->dbconn;
    }

    /**
     * 分析数据库配置信息，支持数组和DSN
     * @access private
     * @param mixed $db_config 数据库配置信息
     * @return string
     */
    private function parseConfig($db_config = '') {
        if (!empty($db_config) && is_string($db_config)) {
            // 如果DSN字符串则进行解析
            $db_config = $this->parseDSN($db_config);
        } elseif (is_array($db_config)) { // 数组配置
            $db_config = array_change_key_case($db_config);
            $db_config = array(
                'username' => $db_config['username'],
                'password' => $db_config['password'],
                'hostname' => $db_config['hostname'],
                'database' => $db_config['database'],
            );
        } elseif (empty($db_config)) {
            $db_config = $this->config;
        }
        return $db_config;
    }

    /**
     * DSN解析
     * 格式： mysql://username:passwd@localhost:3306/DbName
     * @static
     * @access public
     * @param string $dsnStr
     * @return array
     */
    private function parseDSN($dsnStr) {
        if (empty($dsnStr)) {
            return false;
        }
        $info = parse_url($dsnStr);
        if ($info['scheme']) {
            $dsn = array(
                'dbms' => $info['scheme'],
                'username' => isset($info['user']) ? $info['user'] : '',
                'password' => isset($info['pass']) ? $info['pass'] : '',
                'hostname' => isset($info['host']) ? $info['host'] : '',
                'hostport' => isset($info['port']) ? $info['port'] : '',
                'database' => isset($info['path']) ? substr($info['path'], 1) : ''
            );
        } else {
            preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/', trim($dsnStr), $matches);
            $dsn = array(
                'dbms' => $matches[1],
                'username' => $matches[2],
                'password' => $matches[3],
                'hostname' => $matches[4],
                'hostport' => $matches[5],
                'database' => $matches[6]
            );
        }
        $dsn['dsn'] = ''; // 兼容配置信息数组
        return $dsn;
    }

    /**
     * set分析
     * @access protected
     * @param array $data
     * @return string
     */
    private function parseSet($data) {
        foreach ($data as $key => $val) {
            $value = $this->parseValue($val);
            if (is_scalar($value)) // 过滤非标量数据
                $set[] = $this->parseKey($key) . '=' . $value;
        }
        return ' SET ' . implode(',', $set);
    }

    /**
     * 字段名分析
     * @access protected
     * @param string $key
     * @return string
     */
    private function parseKey(&$key) {
        return $this->escapeString($key);
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    private function parseValue($value) {
        if (is_string($value)) {
            $value = '\'' . $this->escapeString($value) . '\'';
        } elseif (isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp') {
            $value = $this->escapeString($value[1]);
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * field分析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    private function parseField($fields) {
        if (is_string($fields) && strpos($fields, ',')) {
            $fields = explode(',', $fields);
        }
        if (is_array($fields)) {
            // 完善数组方式传字段名的支持
            // 支持 'field1'=>'field2' 这样的字段别名定义
            $array = array();
            foreach ($fields as $key => $field) {
                if (!is_numeric($key))
                    $array[] = $this->parseKey($key) . ' AS ' . $this->parseKey($field);
                else
                    $array[] = $this->parseKey($field);
            }
            $fieldsStr = implode(',', $array);
        } elseif (is_string($fields) && !empty($fields)) {
            $fieldsStr = $this->parseKey($fields);
        } else {
            $fieldsStr = '*';
        }
        return $fieldsStr;
    }

    /**
     * where分析
     * @access protected
     * @param mixed $where
     * @return string
     */
    public function parseWhere($where) {
        $whereStr = '';
        if (is_string($where)) {
            // 直接使用字符串条件
            $whereStr = $where;
        } else { // 使用数组表达式
            $operate = isset($where['_logic']) ? strtoupper($where['_logic']) : '';
            if (in_array($operate, array('AND', 'OR', 'XOR'))) {
                // 定义逻辑运算规则 例如 OR XOR AND NOT
                $operate = ' ' . $operate . ' ';
                unset($where['_logic']);
            } else {
                // 默认进行 AND 运算
                $operate = ' AND ';
            }
            foreach ($where as $key => $val) {
                $whereStr .= '( ';
                if (0 === strpos($key, '_')) {
                    // 解析特殊条件表达式
                    $whereStr .= $this->parseThinkWhere($key, $val);
                } else {
                    // 查询字段的安全过滤
                    if (!preg_match('/^[A-Z_\|\&\-.a-z0-9\(\)\,]+$/', trim($key))) {
                        return $this->setErrMessage(new Exception('表达式错误:' . $key));
                    }
                    // 多条件支持
                    $multi = is_array($val) && isset($val['_multi']);
                    $key = trim($key);
                    if (strpos($key, '|')) { // 支持 name|title|nickname 方式定义查询字段
                        $array = explode('|', $key);
                        $str = array();
                        foreach ($array as $m => $k) {
                            $v = $multi ? $val[$m] : $val;
                            $str[] = '(' . $this->parseWhereItem($this->parseKey($k), $v) . ')';
                        }
                        $whereStr .= implode(' OR ', $str);
                    } elseif (strpos($key, '&')) {
                        $array = explode('&', $key);
                        $str = array();
                        foreach ($array as $m => $k) {
                            $v = $multi ? $val[$m] : $val;
                            $str[] = '(' . $this->parseWhereItem($this->parseKey($k), $v) . ')';
                        }
                        $whereStr .= implode(' AND ', $str);
                    } else {
                        $whereStr .= $this->parseWhereItem($this->parseKey($key), $val);
                    }
                }
                $whereStr .= ' )' . $operate;
            }
            $whereStr = substr($whereStr, 0, -strlen($operate));
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }

    // where子单元分析
    private function parseWhereItem($key, $val) {
        $whereStr = '';
        if (is_array($val)) {
            if (is_string($val[0])) {
                if (preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT)$/i', $val[0])) { // 比较运算
                    $whereStr .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' ' . $this->parseValue($val[1]);
                } elseif (preg_match('/^(NOTLIKE|LIKE)$/i', $val[0])) {// 模糊查找
                    if (is_array($val[1])) {
                        $likeLogic = isset($val[2]) ? strtoupper($val[2]) : 'OR';
                        if (in_array($likeLogic, array('AND', 'OR', 'XOR'))) {
                            $likeStr = $this->comparison[strtolower($val[0])];
                            $like = array();
                            foreach ($val[1] as $item) {
                                $like[] = $key . ' ' . $likeStr . ' ' . $this->parseValue($item);
                            }
                            $whereStr .= '(' . implode(' ' . $likeLogic . ' ', $like) . ')';
                        }
                    } else {
                        $whereStr .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' ' . $this->parseValue($val[1]);
                    }
                } elseif ('exp' == strtolower($val[0])) { // 使用表达式
                    $whereStr .= ' (' . $key . ' ' . $val[1] . ') ';
                } elseif (preg_match('/IN/i', $val[0])) { // IN 运算
                    if (isset($val[2]) && 'exp' == $val[2]) {
                        $whereStr .= $key . ' ' . strtoupper($val[0]) . ' ' . $val[1];
                    } else {
                        if (is_string($val[1])) {
                            $val[1] = explode(',', $val[1]);
                        }
                        $zone = implode(',', $this->parseValue($val[1]));
                        $whereStr .= $key . ' ' . strtoupper($val[0]) . ' (' . $zone . ')';
                    }
                } elseif (preg_match('/BETWEEN/i', $val[0])) { // BETWEEN运算
                    $data = is_string($val[1]) ? explode(',', $val[1]) : $val[1];
                    $whereStr .= ' (' . $key . ' ' . strtoupper($val[0]) . ' ' . $this->parseValue($data[0]) . ' AND ' . $this->parseValue($data[1]) . ' )';
                } else {
                    return $this->setErrMessage(new Exception('表达式错误:' . $val[0]));
                }
            } else {
                $count = count($val);
                $rule = isset($val[$count - 1]) && is_string($val[$count - 1]) ? strtoupper($val[$count - 1]) : '';
                if (in_array($rule, array('AND', 'OR', 'XOR'))) {
                    $count = $count - 1;
                } else {
                    $rule = 'AND';
                }
                for ($i = 0; $i < $count; $i++) {
                    $data = is_array($val[$i]) ? $val[$i][1] : $val[$i];
                    if ('exp' == strtolower($val[$i][0])) {
                        $whereStr .= '(' . $key . ' ' . $data . ') ' . $rule . ' ';
                    } else {
                        $op = is_array($val[$i]) ? $this->comparison[strtolower($val[$i][0])] : '=';
                        $whereStr .= '(' . $key . ' ' . $op . ' ' . $this->parseValue($data) . ') ' . $rule . ' ';
                    }
                }
                $whereStr = substr($whereStr, 0, -4);
            }
        } else {
            $whereStr .= $key . ' = ' . $this->parseValue($val);
        }
        return $whereStr;
    }

    /**
     * 特殊条件分析
     * @access protected
     * @param string $key
     * @param mixed $val
     * @return string
     */
    private function parseThinkWhere($key, $val) {
        $whereStr = '';
        switch ($key) {
            case '_string':
                // 字符串模式查询条件
                $whereStr = $val;
                break;
            case '_complex':
                // 复合查询条件
                $whereStr = substr($this->parseWhere($val), 6);
                break;
            case '_query':
                // 字符串模式查询条件
                parse_str($val, $where);
                if (isset($where['_logic'])) {
                    $op = ' ' . strtoupper($where['_logic']) . ' ';
                    unset($where['_logic']);
                } else {
                    $op = ' AND ';
                }
                $array = array();
                foreach ($where as $field => $data)
                    $array[] = $this->parseKey($field) . ' = ' . $this->parseValue($data);
                $whereStr = implode($op, $array);
                break;
        }
        return $whereStr;
    }

    /**
     * limit分析
     * @access protected
     * @param mixed $lmit
     * @return string
     */
    private function parseLimit($limit) {
        return !empty($limit) ? ' LIMIT ' . $this->escapeString($limit) . ' ' : '';
    }

    /**
     * order分析
     * @access protected
     * @param mixed $order
     * @return string
     */
    private function parseOrder($order) {
        if (is_array($order)) {
            $array = array();
            foreach ($order as $key => $val) {
                if (is_numeric($key)) {
                    $array[] = $this->parseKey($val);
                } else {
                    $array[] = $this->parseKey($key) . ' ' . $val;
                }
            }
            $order = implode(',', $array);
        } else {
            $order = $this->escapeString($order);
        }
        return !empty($order) ? ' ORDER BY ' . $order : '';
    }

    /**
     * group分析
     * @access protected
     * @param mixed $group
     * @return string
     */
    private function parseGroup($group) {
        return !empty($group) ? ' GROUP BY ' . $this->escapeString($group) : '';
    }

    /**
     * 插入记录
     * @access public
     * @param mixed $data 数据
     * @return false | integer
     */
    public function insert($data) {
        if (!$this->table)
            return false;
        $values = $fields = array();
        foreach ($data as $key => $val) {
            $value = $this->parseValue($val);
            if (is_scalar($value)) { // 过滤非标量数据
                $values[] = $value;
                $fields[] = $this->parseKey($key);
            }
        }
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
        $this->queryStr = $sql;
//        Log::debug(__FUNCTION__, $sql);
        $result = $this->dbconn->query($sql);
        if ($result) {
            return $this->dbconn->insert_id;
        } else {
            return $this->setErrMessage(new Exception($this->dbconn->error . " insert run sql:" . $sql));
        }
    }

    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @param array $where 判断条件
     * @return false | integer
     */
    public function update($data, $where) {
        if (!$this->table || !$data)
            return false;
        $sql = 'UPDATE '
                . $this->table
                . $this->parseSet($data)
                . $this->parseWhere(!empty($where) ? $where : '');
        $this->queryStr = $sql;
//        Log::debug(__FUNCTION__, $sql);
        $result = $this->dbconn->query($sql);
        if ($result) {
            return $this->dbconn->affected_rows;
        } else {
            return $this->setErrMessage(new Exception($this->dbconn->error . " update run sql:" . $sql));
        }
    }

    /**
     * 删除记录
     * @access public
     * @param array $where 判断条件
     * @return false | integer
     */
    public function delete($where) {
        if (!$this->table)
            return false;
        $sql = 'DELETE FROM '
                . $this->table
                . $this->parseWhere(!empty($where) ? $where : '');
        $this->queryStr = $sql;
//        Log::debug(__FUNCTION__, $sql);
        $result = $this->dbconn->query($sql);
        if ($result) {
            return $this->dbconn->affected_rows;
        } else {
            return $this->setErrMessage(new Exception($this->dbconn->error . " delete run sql:" . $sql));
        }
    }

    /**
     * 查找记录
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
        if (!$this->table)
            return false;
        $options = array(
            'table' => $this->table,
        );
        if ($fields)
            $options['field'] = $fields;
        if ($where)
            $options['where'] = $where;
        if ($order)
            $options['order'] = $order;
        if ($limit)
            $options['limit'] = $limit;
        if ($skip)
            $options['skip'] = $skip;
        if ($group)
            $options['group'] = $group;
        $sql = $this->buildSelectSql($options);

        $this->queryStr = $sql;
        $result = $this->dbconn->query($sql);
        if ($result) {
            $ret = array();
            while ($row = $result->fetch_assoc()) {
                $ret[] = $row;
            }
            $result->free_result();
        } else {
            $ret = $this->setErrMessage(new Exception($this->dbconn->error . " select run sql:" . $sql));
        }
        return $ret;
    }

    /**
     * 生成查询SQL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    private function buildSelectSql($options = array()) {
        if (isset($options['skip']) && !empty($options['skip'])) {
            $options['limit'] = $options['skip'] . ',' . $options['limit'];
        }
        $sql = $this->parseSql($this->selectSql, $options);
//        Log::debug(__FUNCTION__, $sql);
        return $sql;
    }

    /**
     * 替换SQL语句中表达式
     * @access public
     * @param array $options 表达式
     * @return string
     */
    private function parseSql($sql, $options = array()) {
        $sql = str_replace(
                array('%TABLE%', '%FIELD%', '%WHERE%', '%GROUP%', '%ORDER%', '%LIMIT%'), array(
            $this->table,
            $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
            $this->parseWhere(!empty($options['where']) ? $options['where'] : ''),
            $this->parseGroup(!empty($options['group']) ? $options['group'] : ''),
            $this->parseOrder(!empty($options['order']) ? $options['order'] : ''),
            $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ''),
                ), $sql);
        return $sql;
    }

    /**
     * SQL查询
     * @access public
     * @param string $sql SQL指令
     * @return mixed
     */
    public function excuteSql($sql) {
        //有sql 注入风险!
        $this->queryStr = $sql;
//        Log::debug(__FUNCTION__, $sql);
        $queryResult = $this->dbconn->query($sql);
        if (true === $queryResult) {
            //insert
            if ($this->dbconn->insert_id) {
                return $this->dbconn->insert_id;
            }
            //update和delete
            return $this->dbconn->affected_rows;
        } elseif (is_object($queryResult)) {
            //select
            $ret = array();
            while ($row = $queryResult->fetch_assoc()) {
                $ret[] = $row;
            }
            $queryResult->free_result();
            return $ret;
        } else {
            return $this->setErrMessage(new Exception($this->dbconn->error . " run sql:" . $sql));
        }
    }

    /**
     * 取得数据表的字段信息
     * @access public
     * @return array
     */
    public function getFields($tableName) {
        $result = $this->dbconn->query('SHOW COLUMNS FROM ' . $this->parseKey($tableName));
        $ret = array();
        while ($row = $result->fetch_assoc()) {
            $ret[] = $row;
        }
        $result->free_result();
        $info = array();
        if ($ret) {
            foreach ($result as $key => $val) {
                $info[$val['Field']] = array(
                    'name' => $val['Field'],
                    'type' => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }

    /**
     * Pings a server connection, or tries to reconnect if the connection has gone down
     * @access public
     * @return
     */
    public function ping() {
        return $this->dbconn->ping();
    }

    /**
     * 获取当前查询sql语句
     * @access public
     * @return string
     */
    public function getQuerySql() {
        return $this->queryStr;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str SQL字符串
     * @return string
     */
    public function escapeString($str) {
        if (!is_string($str))
            return $str;
        return $this->dbconn->real_escape_string($str);
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
        // 关闭连接
        $this->close();
    }

    // 关闭数据库 由驱动类定义
    public function close() {
        if ($this->dbconn) {
            $this->dbconn->close();
        }
        $this->dbconn = null;
    }

}
