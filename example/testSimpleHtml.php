<?php

/**
 * Description of testSimpleHtml
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-30 09:16:42
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';
use Joyme\net\Simple_html_dom;


$html = new Simple_html_dom();
$html->load(file_get_contents('http://www.joyme.com'));


//echo $html->__toString();
foreach($html->find('img') as $img){
    echo $img->src."\n";
}
