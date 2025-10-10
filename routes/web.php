<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Services\MailService;

Route::get('/mail-test', function () {
    $html = "<h3>Test Mail</h3><p>This is a PHPMailer test email from Laravel.</p>";
    $sent = MailService::send('crisjustineoracion146@gmail.com', 'PHPMailer Test', $html, 'Mail Tester');

    return $sent
        ? '✅ Email sent successfully!'
        : '❌ Failed to send (check storage/logs/laravel.log)';
});