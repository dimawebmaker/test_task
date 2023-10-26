<?php

require_once("./vendor/autoload.php");

use DiDom\Document;

DB::$user = 'root';
DB::$password = '';
DB::$dbName = 'bills';
DB::$encoding = 'utf-8';


$response = '';

try {
    $ch = curl_init('https://www.bills.ru/news/');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);

} catch (Exception $e) {
    echo 'Curl exception: ' . $e->getMessage();
}


$page = new Document($response);

if( !$page->has('ul.news_feed') ) {
    throw new Exception("Error no news feed.");
}

$feed = $page->first('ul.news_feed');
foreach($feed->find('li.news_item.news_item_standart') as $news) {
    $temp = strtotime(trim($news->first('span.timestamp')->text()));

    $title = trim($news->first('td.news_title')->first('a')->text());
    $date = date("Y-m-d H:i:s", $temp);
    $url = trim($news->first('td.news_title')->first('a')->attr('href'));
    
    $is_exists = DB::queryFirstField("select id from bills_ru_events where url=%s", $url);
    if($is_exists) continue;
    DB::insert('bills_ru_events', [
        'title' => $title,
        'date' => $date,
        'url' => $url
      ]);
      echo DB::insertId(), "\n";
}

// if(!$page->has('table.'));