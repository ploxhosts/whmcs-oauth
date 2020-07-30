<?php

require __DIR__ . '/init.php';

session_start();

// Upon discord letting us in
if(isset($_GET['code'])) {
	$url = "https://discord.com/api/oauth2/token";

	$fields = [
	    'client_id'         => '',
	    'client_secret'     => '',
	    'grant_type'        => 'authorization_code',
	    'code'         		=> $_GET['code'],
	    'redirect_uri'      => 'https://' . $_SERVER['SERVER_NAME'] . '/discord.php',
	    'scope'         	=> 'identify',
	];

	$fields_string = http_build_query($fields);
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	$data = json_decode($result);

	$_SESSION['access'] = $data->access_token;
// 	echo 'got code getting token';
	header("Location: discord.php");
}

// After getting the token, get details
elseif(isset($_SESSION['access'])) {
	$url = "https://discord.com/api/v6/users/@me";
	$ch = curl_init($url);

	$headers[] = 'Authorization: Bearer ' . $_SESSION['access'];

	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
	$data = json_decode($response);

	$_SESSION['did'] = $data->id;

	unset($_SESSION['access']);
// 	echo 'geting user info';
	header("Location: discord.php");
}

elseif(isset($_SESSION['username'])) {
	$clientId = $_SESSION['uid'];
	$discordId = rawurlencode($_SESSION['did']);

	$url = "SEND REQUEST HERE! clientId=$clientId&id=$discordId";

    $data = file_get_contents($url);
    if($data == "works") {
        header('Location: https://' . $_SERVER['SERVER_NAME'] . '/clientarea.php');
    } else {
        header('Location: https://' . $_SERVER['SERVER_NAME'] . '/clientarea.php');
    }

} else {
    echo 'oopsie';
    unset($_SESSION['username']);
    unset($_SESSION['discriminator']);
    unset($_SESSION['access']);
    unset($_SESSION['token']);
    unset($_SESSION['clientId']);
}
?>
