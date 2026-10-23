<?php
// Token va ID'larni kodga yozmang: serverda muhit o'zgaruvchisi sifatida saqlang.
// Misol: hosting panelida BOT_TOKEN va ADMIN_ID o'zgaruvchilarini kiriting.
$token    = getenv('BOT_TOKEN') ?: 'BU_YERGA_YANGI_TOKEN';   // faqat sinov uchun
$admin_id = getenv('ADMIN_ID')  ?: '';                        // o'zingizning Telegram ID'ingiz
$app_url  = 'https://SIZNING-SAYT.vercel.app';                // Vercel havolangiz (https bo'lishi shart)

function tg($method, $data) {
    global $token;
    $ch = curl_init("https://api.telegram.org/bot{$token}/{$method}");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
        error_log('Telegram xato: ' . curl_error($ch));
    }
    curl_close($ch);
    return $res;
}

$update = json_decode(file_get_contents('php://input'), true);
if (!isset($update['message'])) {
    exit;
}

$message = $update['message'];
$chat_id = $message['chat']['id'];
$text    = $message['text'] ?? '';

if (isset($message['web_app_data'])) {
    // Mini App'dan buyurtma keldi
    $data  = json_decode($message['web_app_data']['data'], true) ?: [];
    $name  = htmlspecialchars($data['product'] ?? "Noma'lum", ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars($data['price'] ?? "Noma'lum", ENT_QUOTES, 'UTF-8');

    tg('sendMessage', [
        'chat_id'    => $chat_id,
        'parse_mode' => 'HTML',
        'text'       => "✅ Buyurtmangiz qabul qilindi!\n\n📦 Mahsulot: <b>{$name}</b>\n💰 Narxi: <b>{$price}</b>\n\nTez orada operatorimiz siz bilan bog'lanadi.",
    ]);

    // Adminga xabar
    if ($admin_id !== '') {
        $user  = $message['from'];
        $uname = htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), ENT_QUOTES, 'UTF-8');
        $login = isset($user['username']) ? '@' . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') : 'username yo\'q';
        tg('sendMessage', [
            'chat_id'    => $admin_id,
            'parse_mode' => 'HTML',
            'text'       => "🆕 Yangi buyurtma\n\n👤 {$uname} ({$login})\n📦 {$name}\n💰 {$price}",
        ]);
    }
} elseif ($text === '/start') {
    // Reply keyboard: sendData faqat shu tugma orqali ishlaydi
    $keyboard = [
        'keyboard' => [[
            ['text' => '🛍 Katalog ochish', 'web_app' => ['url' => $app_url]],
        ]],
        'resize_keyboard' => true,
    ];

    tg('sendMessage', [
        'chat_id'      => $chat_id,
        'text'         => 'Xush kelibsiz! Mahsulotlarni tanlash uchun katalogni oching:',
        'reply_markup' => json_encode($keyboard),
    ]);
}
