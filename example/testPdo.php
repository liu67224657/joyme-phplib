<?php

/**
 * Description of testPdo
 * 
 * 
 * @author kexuedong
 * @date 2015-05-13
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\db\Db;


$config = array(
    'show_error' => '0',//数据库调试中断
    'prefix' => '',//表前缀
    'error_path' => '',//
    'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=test;',
    'user' => 'root',
    'pwd' => '123456',
    'language' => 'utf8',
);

$mysql = new Db($config);

//select 过程

//$ret = $mysql->db_select('test','id=?',array(3));
//$ret = $mysql->db_select('test');
$sql = 'select *from test';
//$bind = array();
//$ret = $mysql->db_fetch_arrays($sql,$bind);
$ret = $mysql->db_fetch_arrays($sql);
var_dump($ret);

//insert 过程

/*$sql = "INSERT INTO test(cateid,title) VALUES (?,?)";
$bind = array(100,"这是我的pdo测试");
$result = $mysql->db_query($sql,'',$bind);

var_dump($result);*/


$mysql->db_close();