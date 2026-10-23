<?php
// Bot tokeningiz
$token = "8699966136:AAHlGLDIP_wPxZGdN_oBthsv0BvuLdaB2Vc";

// Telegram'dan kelayotgan JSON ma'lumotni o'qish
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!isset($update['message'])) {
    exit;
}

$chat_id = $update['message']['chat']['id'];
$text = $update['message']['text'] ?? '';

// 1. /start bosilganda Mini App tugmasini yuborish
if ($text == '/start') {
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => '📱 Mini App-ni ochish',
                    'web_app' => ['url' => 'web_app' => ['url' => 'https://mytelegrambot-smoky.vercel.app'] // <-- Bu yerga Mini App havolangizni yozasiz
                ]
            ]
        ]
    ];

    $post_data = [
        'chat_id' => $chat_id,
        'text' => "Xush kelibsiz! Mini App-ni ochish uchun quyidagi tugmani bosing:",
        'reply_markup' => json_encode($keyboard)
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($post_data));
}

// 2. Mini App'dan ma'lumot kelganda javob berish
if (isset($update['message']['web_app_data'])) {
    $web_app_data = $update['message']['web_app_data']['data'];

    $post_data = [
        'chat_id' => $chat_id,
        'text' => "✅ Mini App'dan ma'lumot qabul qilindi:\n\n" . $web_app_data
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($post_data));
}
?>
