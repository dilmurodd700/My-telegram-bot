<?php
$token = "8699966136:AAHlGLDIP_wPxZGdN_oBthsv0BvuLdaB2Vc";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!isset($update['message'])) {
    exit;
}

$chat_id = $update['message']['chat']['id'];
$text = $update['message']['text'] ?? '';

// 1. /start buyrug'i
if ($text == '/start') {
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => '🛍 Katalog va Savatchani ochish',
                    'web_app' => ['url' => 'https://telegram-mini-app-ingiz.vercel.app'] // Vercel havolangiz
                ]
            ]
        ]
    ];

    $post_data = [
        'chat_id' => $chat_id,
        'text' => "Xush kelibsiz! Savatchadan foydalanib buyurtma berish uchun quyidagi tugmani bosing:",
        'reply_markup' => json_encode($keyboard)
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($post_data));
}

// 2. Mini App'dan savatcha ma'lumotlari kelganda
if (isset($update['message']['web_app_data'])) {
    $web_app_data = $update['message']['web_app_data']['data'];
    $data = json_decode($web_app_data, true);

    $name = $data['name'] ?? 'Noma\'lum';
    $phone = $data['phone'] ?? 'Noma\'lum';
    $total = $data['total'] ?? 0;
    $items = $data['items'] ?? [];

    $items_text = "";
    foreach ($items as $item) {
        $items_text .= "- {$item['name']} ({$item['price']} so'm)\n";
    }

    $reply_text = "🚨 <b>Yangi buyurtma tushdi!</b>\n\n" .
                  "👤 Mijoz: <b>{$name}</b>\n" .
                  "📞 Tel: <b>{$phone}</b>\n\n" .
                  "📦 <b>Mahsulotlar:</b>\n{$items_text}\n" .
                  "💰 <b>Jami summa:</b> {$total} so'm";

    $post_data = [
        'chat_id' => $chat_id,
        'text' => $reply_text,
        'parse_mode' => 'HTML'
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($post_data));
}
?>
