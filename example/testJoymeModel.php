<?php
/**
 * Description of testJoymeModel
 *
 * @author kexuedong
 * @date 2015-04-29
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\db\JoymeModel;

//        $GLOBALS['config']['db']['db_host'] = '172.16.75.65';
//        $GLOBALS['config']['db']['db_port'] = 3306;
//        $GLOBALS['config']['db']['db_user'] = 'root';
//        $GLOBALS['config']['db']['db_password'] = '654321';
//        $GLOBALS['config']['db']['db_name'] = 'wikiurl';

class wikipostsDbModel extends JoymeModel{

    //表字段
    public $fields = array(

    );
    //数据表名称
    public $tableName = 'wiki_posts';
    //数据库配置

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->db_config = array(
            'hostname' => '172.16.75.65',
            'username' => 'root',
            'password' => '654321',
            'database' => 'wikiurl'
        );
        parent::__construct();
    }

}
//
//    $dBcore = new wikipostsDbModel();
//    $fields = "create_time,user_name,page_id,user_id,prise_num";
//    $where = array(
//        'wiki_title'=>'111',
//        'wiki_key'=>'zjsn',
//        'page_namespace'=>1000
//    );
//
//    $result = $dBcore->count();
//
//    var_dump($result);
//    exit;



class exampleModel extends JoymeModel
{

    //表字段
    public $fields = array(
        'id' => 'int', //自增ID
        'name' => 'string', //姓名
        'sex' => 'int', //性别
        'add_time' => 'int', //创建时间
    );
    //数据表名称
    public $tableName = 'example';
    //数据库配置
    protected $db_config = array(
        'hostname' => '127.0.0.1',
        'username' => 'root',
        'password' => '123456',
        'database' => 'test',
    );

    /**
     * 构造函数
     */
//    public function __construct()
//    {
//        
//        echo "exampleModel __construct\n";
//        parent::__construct();
//    }

    /**
     * 业务相关函数
     * @access public
     * @return mixed array | null
     */
    public function getDataById($id)
    {
        if(!is_numeric($id))
            return false;
        return $this->selectRow('*', array('id' =>array('eq',intval($id))));
    }

 
}

$example = new exampleModel();



exit;
/*
$result = $example->getDbFieldsByDB();
var_dump($result);
die;*/

//insert过程
/*$data = array(
    'name' => 'dkx\n\r0&%!@#$%^&*()',
    'sex' => 1,
    'add_time' => time(),
);
$result = $example->insert($data);
//var_dump($result);
die;*/

//var_dump($example->db->getQueryStr());

/*$ret = $example->excuteSql('select *from test');
var_dump($ret);
die;*/

/*$ret = $example->numChange('sex',array('id'=>array('eq',7)),-5);

var_dump($ret);die;
//update过程
$data = array(
    'name' => '终极编程5',
    'sex' => 5,
);
$where= array(
    'id' => array('eq', 5)
);

$result = $example->update($data, $where);

var_dump($result);
die;*/

//delete过程
/*$where = array(
    'id' => array('eq', 9),
);
$result = $example->delete($where);
var_dump($result);*/

//$result = $example->excuteSql('select *from test');
//var_dump($result);



//查询过程
$fields = '*';
$where = array(
    'id' => array('gt', 1),
    'name' => array('like','dkx'),
);
$order = 'id DESC';
$limit = '10';
$skip = '3';
$group = 'id';

$ret = $example->select($fields, $where, $order, $limit, $skip, $group);
var_dump($ret);
//获取当前查询语句
$sql = $example->getQuerySql();
var_dump($sql);
die;

$fields = '*';
$where = array(
    'id' => array('eq', 1),
);
$order = '';
$group = '';

$ret = $example->selectRow($fields, $where = array(), $order, $group);
var_dump($ret);



$result = $example->getDataById('19');
var_dump($result);
die;



