<?php
function callGemini($prompt) {

    $apiKey = "AIzaSyDNoy49L2AgOekk5MbiVwkFP31P94qIxrY";

    // endpoint mới
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];
    
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    // ✅ dùng header thay vì ?key=
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-goog-api-key: " . $apiKey
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $res = curl_exec($ch);

    if (curl_errno($ch)) {
        return "Lỗi CURL: " . curl_error($ch);
    }

    curl_close($ch);

    $json = json_decode($res, true);

    // 🔥 debug lỗi thật
    if (isset($json['error'])) {
    return "Lỗi API: " . $json['error']['message'];
}
    return $json['candidates'][0]['content']['parts'][0]['text']
        ?? "AI không trả lời";
}