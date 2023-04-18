<?php
$TOKEN = "YourBotToken";
$query_id = (isset($_REQUEST['query'])) ? $_REQUEST['query']:'0';
$texto = (isset($_REQUEST['dados'])) ? $_REQUEST['dados']:'error';
$res = array(
        'type'=> 'article',
        'id'=> 0,
        'title'=> 'Result',
        'input_message_content'=> array(
            'message_text'=> $texto
        ),
        'description'=> 'WebApp result'
    );
$res = json_encode($res);
$base_url = "https://api.telegram.org/bot{$TOKEN}/answerWebAppQuery?web_app_query_id={$query_id}&result={$res}";

$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$response = file_get_contents($base_url, false, $context);
echo $response;

?>
