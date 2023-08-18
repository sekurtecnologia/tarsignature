<?php

include('../../../inc/includes.php');
Session::checkLoginUser();

include './phpqrcode.php';
require_once('token.php');


// ID do ticket
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

$url_base = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}";

// URL da página com o ID do ticket como parâmetro
$url = "{$url_base}/plugins/tarsignature/front/ticket.signer.php?id={$id}&session_token={$session_token}&app_token={$app_token}";

ob_start();
QRcode::png($url);
$_qr_in_png = ob_get_contents();
ob_end_clean();
header("Content-type: text/html");
$_qr_in_base64 =  base64_encode($_qr_in_png);

// Mostra o QR code na página
echo json_encode(
    array(
        'success' => $_qr_in_base64 ? true : false,
        'message' => 'QrCode gerado com sucesso',
        'img' => $_qr_in_base64
    )
);
?>