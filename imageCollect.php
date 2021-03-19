<?php
session_start();

set_time_limit(900);
require_once "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$_SESSION['count'] = '999999999999999999999';
$_SESSION['directory'] = 'ring';

$directory = $_SESSION['directory'];

$max_id = $_SESSION['count'];

if(!file_exists('/opt/lampp/htdocs/PHP/tweet'.$directory)){ 
    mkdir('/opt/lampp/htdocs/PHP/tweet'.$directory, 0777);
    chmod('/opt/lampp/htdocs/PHP/tweet'.$directory, 0777);
}

$consumer_key = '';
$consumer_secret = '';
$access_token = '';
$access_token_secret = '';

$query ='#リングフィットアドベンチャー 1018 OR @99999　-RT filter:twimg since:2020-10-01 until:2020-10-31';

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

$statuses = $connection->get('search/tweets',['q' => $query, 'max_id' => $max_id, 'result_type'=>'mixed', 'count' => '100', 'tweet_mode' => 'extended', 'include_entities' => true]);



echo '初期ツイートID'.$max_id.'<br>';

if(isset($statuses->errors)){
	echo 'Error occurred.';
	echo 'Error message:' .$statuses->errors[0]->message;
}else{
	if(count($statuses->statuses)==0)
		echo '該当するツイートはありません';
}

$num = 1;
while($num < 3){
  echo $num .'回目の処理です';
  $no = 1;
  foreach($statuses->statuses as $tweet){
		echo '<p>';
    echo 'ステータスID:' . $tweet->id . '<br>';
    echo '名前:' . $tweet->user->name . '<br>';
    echo 'ユーザー名(screen_name):' . $tweet->user->screen_name . '<br>';
    echo '作成日:' . date('Y-m-d H:i:s', strtotime($tweet->created_at)) . '<br>';
    echo '</p>';

    foreach ($tweet->extended_entities->media as $tweet_media) { ?>
      <img src="<?php echo $tweet_media->media_url; ?>" width="50%">
      <?php
        echo '<strong>保存した<br></strong>';
      	$url = $tweet_media->media_url;
      	$data = file_get_contents($url);
      	file_put_contents('/opt/lampp/htdocs/PHP/tweet'.$directory.'/'.$tweet_media->id.'-'.$no.'.jpg',$data);
      	$no += 1;
    }
    if($tweet === end($statuses->statuses)){
      echo $num .'回目の最後のツイートです<br/>';
      $_SESSION['count'] = $tweet->id;
      $max_id = $_SESSION['count'];
      $statuses = $connection->get('search/tweets',['q' => $query, 'max_id' => $max_id, 'result_type'=>'mixed', 'count' => '100', 'tweet_mode' => 'extended', 'include_entities' => true]);
    }
  }
  $num += 1;
}
?>