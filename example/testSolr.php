<?php

/**
 * Description of testSolr
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-30 05:08:01
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\net\solr\Apache_Solr_Service;
use Joyme\net\solr\Apache_Solr_Document;

$solr = new Apache_Solr_Service('172.16.75.30', 38000, '/solr/wiki-page');
echo $solr->getPingUrl() . "\n";
if (!$solr->ping()) {
    echo("service not responding\n");
} else {
    echo("solr Service is available\n");
}
$doc = new Apache_Solr_Document();
$doc->id = 1000;
$doc->title = "测试solr php client " . date("Ymd H:i:s");
$doc->wikikey = 'op';


//提交，更新
$solr->addDocument($doc);
$solr->commit();
echo "commit succss \n";

//查询
$offset = 0;
$limit = 10;
$query = "id:1000";
$response = $solr->search($query, $offset, $limit);
if ($response == null) {
    echo $solr->getErrMessage() . "\n";
} else {
    if ($response->getHttpStatus() == 200) {
        echo "search ok\n";
        echo "find " . $response->response->numFound . "\n";
        foreach ($response->response->docs as $doc) {
            echo "id: " . $doc->id . "\n";
            echo "title: " . $doc->title . "\n";
            echo "wikikey: " . $doc->wikikey . "\n";
        }
    }
}

//分词查询
$query = "分词结果:";
$text = "这里所有的内容都是众多海贼爱好者一起无偿创作的，每一个人都可以直接编辑我们的所有内容，我们有一个秘密基地(点我有几率进入)，在这里可以寻求各种帮助和
    找人在这里我们都在做些什么：添加图片、写文章、修订词条、建立自己的个人页面，尽情发挥，在海贼WIKI里留下你的足迹，建立文章传送门：【Wiki文章的建立】
5.最后一个秘密是我们小小的野心：希望能够让海贼WIKI成为第一个完全由所有热爱海贼的人共同建立的，最大最牛的海贼大全，把成就带给所有的海贼迷们!";
$text = '中华人民共和国';
$response = $solr->analysis($text, 'textIKAnalyze');  //textIKAnalyze ：字段类型
if ($response == null) {
    echo $solr->getErrMessage() . "\n";
} else {
    foreach ($response->analysis->field_types->textIKAnalyze->query as $querys) {
        foreach ($querys as $q) {
            $query.=$q->text . " ";
        }
    }
}
echo $query . "\n";

