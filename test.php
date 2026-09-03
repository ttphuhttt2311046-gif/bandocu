<?php

$apiKey = "API_KEY_CUA_BAN";

$url = "https://generativelanguage.googleapis.com/v1beta/models";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-goog-api-key: $apiKey"
]);

$res = curl_exec($ch);

curl_close($ch);

echo "<pre>";
print_r(json_decode($res, true));