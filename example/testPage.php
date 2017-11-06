<?php
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\page\Page;

//<div class="main_page">
    //<span class="首页"><a  href="javascript:my_test('/example/testPage.php?pb_page=1')">首页</a></span><span class="prev_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=1')">上一页</a></span><span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=1')">1</a></span>
//<span class="now_page active"><a href="javascript:void(0);">2</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=3')">3</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=4')">4</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=5')">5</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=6')">6</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=7')">7</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=8')">8</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=9')">9</a></span>
//<span class="now_page"><a  href="javascript:my_test('/example/testPage.php?pb_page=10')">10</a></span>
//<span class="下一页"><a  href="javascript:my_test('/example/testPage.php?pb_page=3')">下一页</a></span><span class="尾页">
//<a  href="javascript:my_test('/example/testPage.php?pb_page=100')">尾页</a></span><span class="total_num" ><a href="javascript:void(0);">共1000条</a></span></div>

$pb_page = 2;            //获取当前页码
$perpage = 10;           //每页显示条数
$pagebarnum = 10;        //显示分页数显示多少条
$url = "";               //设置置顶URL
$totle = 1000;           //设置总数
$condition = array();   //拼接条件
$classNmae = array(
            'main_page'=>'23eweew',
            'first_page'=>1,
            'prev_page'=>1,
            'now_page'=>1,
            'active'=>1,
            'next_page'=>1,
            'last_page'=>1,
            'total_num'=>1,
    );   //自定义类名

$_page = new Page(array('total' => $totle,'perpage'=>$perpage,'nowindex'=>$pb_page,'url'=>$url,'pagebarnum'=>$pagebarnum,'ajax'=>"my_test"));
$page_str = $_page->show(2,$condition);

print_r($page_str);

?>