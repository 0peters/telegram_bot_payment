<?php

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array

function printArray($var){
	if (is_array($var)){
		foreach ($var as $x => $value) {
		  if (!is_array($value)){
			  echo "$x = $value <br>";
		  }
		  else {
			echo "$x = Array(<br>";
			printArray($value);
			echo ")<br>";
		  }
		}
	}
}
// printArray($input);

$payment_data = array(
	'transaction_amount'=> $input['transaction_amount'],
	'token'=> $input['token'],
	'description'=>$input['description'],
	'installments'=> $input['installments'],
	'payment_method_id'=> $input['payment_method_id'],
	'issuer_id'=> $input['issuer_id'],
	'payer'=> array(
		'email'=> $input['payer']['email'],
		'identification'=> array(
			'type'=> $input['payer']['identification']['type'], 
			'number'=> $input['payer']['identification']['number']
		)
	)
);

$url = 'http://127.0.0.1:8082/process_payment'; #endereço do servidor python que vai processar o pagamento
$data = $payment_data;

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'method'  => 'POST',
		'header' => "Content-Type: application/json",
        'content' => json_encode($data), //http_build_query($data),
		'ignore_errors' => true
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo $result;

?>