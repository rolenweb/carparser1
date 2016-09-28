<?php

use tools\CurlClient;
use tools\SymfonyParser;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Simplon\Mysql\Mysql;


require 'vendor/autoload.php';
require 'tools/CurlClient.php';
require 'tools/SymfonyParser.php';


$url = 'http://toyota-usa.epc-data.com/';


$content = parsePage($url);

$property = parseProperty($content,'link','ul.category2 h4 a',$url);

var_dump($property);


function parsePage($url)
{	
	$content = (new CurlClient())->setUrl($url)->getContentWithInfo();
	if (empty($content['info']['http_code']) === false) {
		if ($content['info']['http_code'] === 200) {
			if (empty($content['result']) === false) {
				return $content['result'];
			}
		}
	}
	return;
}

function parseProperty($content,$type,$pattern,$url = null)
{
	$parser = (new SymfonyParser)->in($content, (new CurlClient())->getContentType());
	$result = [];
	if ($type === 'link') {
		$nodes = $parser->find($pattern);
		
		foreach ($nodes as $node) {
			$link = new Link($node, $url, 'GET');
			$result[] = $link->getUri();
		}		
	}
	return $result;
}

function connectDb()
{
	require 'tools/db.php';
	
	return new Mysql(
	    $config['host'],
	    $config['user'],
	    $config['password'],
	    $config['database']
	);
}
?>