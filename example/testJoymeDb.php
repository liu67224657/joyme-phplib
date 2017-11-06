<?php
/**
 * Description of testJoymeDb
 *
 * Created by PhpStorm.
 * User: kexuedong
 * Date: 2015/5/14
 * Time: 14:30
 */
function help(){
    $str = <<<EOD
where条件运算符说明
http://www.thinkphp.cn/document/314.html

运算符	          例子	                                       实际查询条件
eq		    id => array('eq',100);	                            id = 100;
neq		    id => array('neq',100);	                            id != 100
gt		    id => array('gt',100);	                            id > 100
egt		    id => array('egt',100);	                            id >= 100
lt		    id => array('lt',100);	                            id < 100
elt		    id => array('elt',100);	                            id <= 100
like	    id => array('like','Admin%');	                    username like 'Admin%'
between	    id => array('between',array(1,8));	                    id BETWEEN 1 AND 8
in		    id => array('in','1,5,8');	                        id in(1,5,8)
not in	    id => array('not in','1,5,8');	                    id not in(1,5,8)
and		    id => array(array('gt',1),array('lt',10),'and');	(id > 1) AND (id < 10)
or		    id => array(array('gt',3),array('lt',10), 'or');	(id > 3) OR (id < 10)

复合查询：
拼接sql语句如：
( id > 1) AND ( ( name like '%thinkphp%') OR ( title like '%thinkphp%') )
方法一：
where['id']  = array('gt',1);
where['_complex'] =array(
    'name' => array('like', '%thinkphp%'),
    'title' => array('like', '%thinkphp%'),
    '_logic' => 'or',
)
方法二
where['id'] = array('gt',1);
where['_string'] = ' (name like "%thinkphp%")  OR ( title like "%thinkphp") ';
最后生成的SQL语句是一致的。
where是个数组
EOD;

    echo $str;
}

require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\db\JoymeDb;

$db_dsn = "mysql://root:123456@localhost:3306/test";
$db = new JoymeDb($db_dsn,'order_test');
//$db_dsn = "mysql://root:654321@172.16.75.65:3306/wikiurl";
//$db = new JoymeDb($db_dsn,'wiki_posts');

//$sql = "INSERT INTO order_test (id,order_id,catename,title) VALUES ('',1003,'insert测试','这是order的insert测试')";
//$sql = "DELETE FROM order_test WHERE ( id = 11 )";
//$sql = "UPDATE order_test SET order_id=1034,title='这是db的update测试测试' WHERE ( id = 12 )";
//$sql = "SELECT * FROM order_test where `id` = 1000 GROUP BY order_id ORDER BY id desc";
/*$result = $db->excuteSql($sql);
var_dump($result);
die;*/

//insert过程
/*$data = array(
    'id'=>'',
    'order_id'=>1003,
    'catename'=>'insert测试',
    'title'=>'这是order的insert测试'
);
$result = $db->insert($data);
var_dump($result);die;*/

//delete过程

/*$where = array(
    'id' =>array('eq',6),
);

$result = $db->delete($where);
var_dump($result);die;*/

//update过程
/*$data = array(
    'order_id'=>1034,
    'title'=>'这是db的update测试测试'
);
$where = array(
    'id' => array('eq',13),
);
$result = $db->update($data,$where);
var_dump($result);die;*/

//select过程

/*
$fields = "create_time,user_name,page_id,user_id,prise_num";
$where = array(
    'wiki_title'=>'343434',
    'wiki_key'=>'local',
    'page_namespace'=>'1000'
);

$result = $db->selectRow($fields,$where);

var_dump($result);

die;*/
//$fields = '*';
//$fields="id,order_id";
$fields="*";
$where = array(
//    'order_id'=>array(array('gt',200),array('lt',1000),'and'),
//    'order_id'=>array(array('lt',200),array('gt',1000),'or'),
//    'id' => array('lt',"'10000'"),
//    '_logic' => 'OR',
//    'title' =>array('neq',''),
//    '_string' => 'order_id >= 200 and order_id <= 1000 or catename like "insert"',
//    'id' => array('between',array(5,10)),
    'id' => array('between','5,10'),
);
//$where = array();
$order = 'id desc';
/*$limit = '3';
$skip = '2';*/
//$group = 'order_id';
$result = $db->select($fields,$where,$order);
//$result = $db->selectRow($fields,$where=array(),$order,$group);
var_dump($result);

var_dump($db->getQuerySql());
die;




