<?php
// Sizning bot tokeningiz
$token = "8699966136:AAHlGLDIP_wPxZGdN_oBthsv0BvuLdaB2Vc";

// Telegram'dan kelayotgan JSON ma'lumotni o'qib olish
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!isset($update['message'])) {
    exit;
}

$chat_id = $update['message']['chat']['id'];
$text = $update['message']['text'] ?? '';

// 1. Agar foydalanuvchi /start buyrug'ini yuborsa
if ($text == '/start') {
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => '🛍 Katalog ochish (Mini App)',
                    'web_app' => ['url' => 'https://telegram-mini-app-ingiz.vercel.app'] // <-- Vercel havolangizni yozing
                ]
            ]
        ]
    ];

    $post_data = [
        'chat_id' => $chat_id,
        'text' => "Xush kelibsiz! Mahsulotlarni tanlash uchun katalogimizni oching:",
        'reply_markup' => json_encode($keyboard)
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($post_data));
}

// 2. Mini App'dan mahsulot buyurtmasi kelganda ishlaydigan qism
if (isset($update['message']['web_app_data'])) {
    $web_app_data = $update['message']['web_app_data']['data'];
    
    // Mini App'dan kelgan JSON ma'lumotni o'qiymiz
    $data_array = json_decode($web_app_data, true);
    
    $product_name = $data_array['product'] ?? 'Noma\'lum';
    $product_price = $data_array['price'] ?? 'Noma\'lum';

    // Foydalanuvchiga tasdiqlash xabarini yuborish
    $reply_text = "✅ Buyurtmangiz qabul qilindi!\n\n" .
                  "📦 Mahsulot: <b>{$product_name}</b>\n" .
                  "💰 Narxi: <b>{$product_price}</b>\n\n" .
                  "Tez orada operatorimiz siz bilan bog'lanadi.";

    $post_data = [
        'chat_id' => $chat_id,
        'text' => $reply_text,
        'parse_mode' => 'HTML'
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($post_data));
    
    // (Ixtiyoriy) O'zingizning admin ID'ingizga ham buyurtma kelishi haqida xabar yuborishingiz mumkin
}
?>

