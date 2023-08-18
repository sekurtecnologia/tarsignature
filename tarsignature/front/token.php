<?php

include('../../../inc/includes.php');

global $DB;

// Obter o token de app_tokens
$result = $DB->query("SELECT app_token FROM glpi_apiclients");
$result = $result->fetch_assoc();
$app_token = $result['app_token'];

// Obter o token de api
$result = $DB->query("SELECT api_token FROM glpi_users");
$result = $result->fetch_assoc();
$api_token = $result['api_token'];

// Se não foi encontrar o app token
if (empty($app_token)) {
  echo json_encode(
    array(
      'success' => false,
      'message' => 'Você precisa gerar um App Token para proceder',
      'session_token' => '',
      'app_token' => $app_token,
      'api_token' => $api_token
    )
  );

  die;
}

// Se não foi possível encontrar a api token
if (empty($api_token)) {
  echo json_encode(
    array(
      'success' => false,
      'message' => 'Você precisa gerar um Token de API para proceder',
      'session_token' => '',
      'app_token' => $app_token,
      'api_token' => $api_token
    )
  );

  die;
}

$curl = curl_init();

global $CFG_GLPI;

$url_base_api = trim($CFG_GLPI['url_base_api'], "/");

curl_setopt_array(
  $curl,
  array(
    CURLOPT_URL => $url_base_api . '/initSession',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Authorization: user_token ' . $api_token,
      'App-Token: ' . $app_token
    ),
  )
);

$response = curl_exec($curl);
curl_close($curl);

// Se não foi possível gerar o token de sessão
if (!$response) {
  echo json_encode(
    array(
      'success' => false,
      'message' => 'Não foi possivel gerar o token de sessão',
      'session_token' => $session_token,
      'app_token' => $app_token,
      'api_token' => $api_token
    )
  );

  die;
}

$arr = json_decode($response, true);

$session_token = $arr['session_token'];