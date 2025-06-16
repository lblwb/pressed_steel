<?php

add_action('phpmailer_init', 'setup_smtp_mailer');
function setup_smtp_mailer($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mail.nic.ru'; // SMTP-сервер (например smtp.yandex.ru, smtp.gmail.com и т.п.)
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 465;           // 465 для SSL или 587 для TLS
    $phpmailer->Username = 'site@pressedsteel.ru'; // твой email
    $phpmailer->Password = '0hUtXezcWYZzU0l';      // Пароль от SMTP
    $phpmailer->SMTPSecure = 'ssl';         // либо 'tls' для порта 587
    $phpmailer->From = 'site@pressedsteel.ru';
    $phpmailer->FromName = 'Pressed Steel [SITE]';       // Имя отправителя
}

function pressedsteel_send_form()
{
    // Verify nonce
//    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pressedsteel_form_nonce')) {
//        wp_send_json_error('Неверный запрос');
//    }

    // Get and sanitize form type
    $form_type = sanitize_text_field($_POST['formType'] ?? '');

    // Validate form type
    if (!in_array($form_type, ['poupup', 'poupup_consult'])) {
        wp_send_json_error('Недопустимый тип формы');
    }

    // Sanitize form fields
    $fullname = sanitize_text_field($_POST['fullname'] ?? '');
    $company = sanitize_text_field($_POST['company'] ?? '');
    $inn = sanitize_text_field($_POST['inn'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $comment = sanitize_textarea_field($_POST['comment'] ?? '');

//    var_dump($_POST);

    // Validate required fields
    if (empty($fullname) || empty($phone) || empty($email)) {
        wp_send_json_error('Обязательные поля (ФИО, телефон, email) не заполнены');
    }

    // Build email body
    $subject = sprintf('Новая заявка с сайта (тип заявки: %s)', $form_type == 'poupup' ? 'CTA' : 'Консультация');
    $body = "<p><strong>Форма:</strong> {$form_type}</p>";
    $body .= "<p><strong>ФИО:</strong> {$fullname}</p>";
    if ($form_type === 'poupup') {
        $body .= "<p><strong>Компания:</strong> {$company}</p>";
        $body .= "<p><strong>ИНН:</strong> {$inn}</p>";
    }
    $body .= "<p><strong>Телефон:</strong> {$phone}</p>";
    $body .= "<p><strong>Email:</strong> {$email}</p>";
    if ($comment) {
        $body .= "<p><strong>Комментарий:</strong> {$comment}</p>";
    }



    $from_name = "Pressed Steel Site";
    $from_email = "site@pressedsteel.ru";
    // Email headers
    $headers = [
        'From: '.$from_name.' <'.$from_email.'>',
        'Reply-To: ' . $email,
        'Content-Type: text/html; charset=UTF-8',
    ];

    // Handle file upload for poupup form
    $attachments = [];
    if ($form_type === 'poupup' && !empty($_FILES['file']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($_FILES['file']['type'], $allowed_types)) {
            wp_send_json_error('Недопустимый тип файла. Допустимы только PDF, JPEG или PNG');
        }
        // Restrict file size (e.g., 5MB)
        if ($_FILES['file']['size'] > 5 * 1024 * 1024) {
            wp_send_json_error('Файл слишком большой. Максимальный размер: 5 МБ');
        }
        $upload_overrides = ['test_form' => false];
        $uploaded = wp_handle_upload($_FILES['file'], $upload_overrides);
        if (isset($uploaded['error'])) {
            wp_send_json_error('Ошибка загрузки файла: ' . $uploaded['error']);
        }
        $attachments[] = $uploaded['file'];
    }

    // Send email
    $to = 'info@pressedsteel.ru';
//    $to = 'site@pressedsteel.ru';
//    $to = 'cq3vanoj1d@ibolinva.com';
    $sent = wp_mail($to, $subject, $body, $headers, $attachments);

    //Send client

    // Отправляем клиенту подтверждение
    $form_type_client = ($form_type === 'poupup') ? 'на предварительную оценку' : 'на консультацию';
    $subject_client = "Спасибо за вашу заявку — {$form_type_client} на сайте Pressed Steel";


    $body_client = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Спасибо за заявку</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

    /* Общие стили */
    body, html {
        margin: 0; padding: 0; width: 100%; height: 100%;
        background-color: #f5f8ff;
        font-family: 'Montserrat', sans-serif;
        color: #333;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .email-wrapper {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0, 113, 254, 0.15);
        overflow: hidden;
    }
    .email-header {
        background-color: #0071FE;
        padding: 24px 32px;
        text-align: center;
        color: #fff;
        font-weight: 700;
        font-size: 28px;
        letter-spacing: 0.03em;
        box-shadow: 0 4px 12px rgba(0, 113, 254, 0.3);
    }
    .email-body {
        padding: 32px 40px;
        font-size: 16px;
        line-height: 1.6;
    }
    .email-body h2 {
        font-weight: 700;
        font-size: 24px;
        margin-top: 0;
        margin-bottom: 16px;
        color: #0071FE;
    }
    .email-body p {
        margin-bottom: 20px;
        color: #4a4a4a;
    }
    .highlight {
        color: #0071FE;
        font-weight: 600;
    }
    .btn-primary {
        display: inline-block;
        background: linear-gradient(90deg, #005bb5 0%, #0071FE 100%);
        color: #fff !important;
        padding: 14px 36px;
        font-weight: 600;
        font-size: 16px;
        text-decoration: none;
        border-radius: 28px;
        box-shadow: 0 6px 20px rgba(0, 113, 254, 0.35);
        transition: background 0.3s ease;
        user-select: none;
        margin-top: 10px;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #004a99 0%, #005bb5 100%);
    }
    .email-footer {
        background-color: #f0f4ff;
        padding: 24px 32px;
        font-size: 14px;
        text-align: center;
        color: #666;
        border-top: 1px solid #d7defb;
        user-select: none;
    }
    .email-footer a {
        color: #0071FE;
        font-weight: 600;
        text-decoration: none;
    }

    /* Адаптив для мобильных */
    @media (max-width: 640px) {
        .email-wrapper {
            margin: 12px;
        }
        .email-header {
            font-size: 22px;
            padding: 20px 24px;
        }
        .email-body {
            padding: 24px 24px;
            font-size: 15px;
        }
        .email-body h2 {
            font-size: 20px;
        }
        .btn-primary {
            padding: 12px 28px;
            font-size: 15px;
            border-radius: 24px;
        }
        .email-footer {
            padding: 20px 24px;
            font-size: 13px;
        }
    }
</style>
</head>
<body>
    <div class="email-wrapper" role="main" aria-label="Письмо с подтверждением заявки">
        <header class="email-header" aria-label="Заголовок письма">
            Спасибо за заявку!
        </header>
        <section class="email-body">
            <h2>Здравствуйте, {$fullname}!</h2>
            <p>Благодарим за заявку <span class="highlight">{$form_type_client}</span> на сайте <strong>Pressed Steel</strong>.</p>
            <p>Мы уже начали обрабатывать ваш запрос и свяжемся с вами в ближайшее время для уточнения деталей.</p>
            <p>Если у вас возникнут вопросы — просто ответьте на это письмо, мы всегда на связи.</p>
            <a href="mailto:site@pressedsteel.ru" class="btn-primary" target="_blank" rel="noopener noreferrer" aria-label="Связаться с командой Pressed Steel">Связаться с нами</a>
        </section>
        <footer class="email-footer" aria-label="Контактная информация">
            С уважением,<br>
            Команда <strong>Pressed Steel</strong><br>
            <a href="https://pressedsteel.ru" target="_blank" rel="noopener noreferrer" aria-label="Перейти на сайт Pressed Steel">pressedsteel.ru</a>
        </footer>
    </div>
</body>
</html>
HTML;


    $headers_client = [
        'From: '.$from_name.' <'.$from_email.'>',
        'Content-Type: text/html; charset=UTF-8',
    ];

    // Отправляем письмо клиенту, ошибки игнорируем, чтобы не ломать успешный результат
    wp_mail($email, $subject_client, $body_client, $headers_client);

    if ($sent) {
        wp_send_json_success('Письмо отправлено');
    } else {
        $error = error_get_last();
        wp_send_json_error('Не удалось отправить письмо: ' . ($error['message'] ?? 'Неизвестная ошибка'));
    }

    wp_die();
}

//add_action('wp_ajax_pressedsteel_handle_form', 'pressedsteel_handle_form');
//add_action('wp_ajax_nopriv_pressedsteel_send_form', 'pressedsteel_send_form');

add_action('rest_api_init', function () {
    register_rest_route('pst/v1', '/handle-form', [
        'methods' => WP_REST_Server::CREATABLE, // Эквивалент 'POST'
        'callback' => 'pressedsteel_send_form',
        'permission_callback' => '__return_true', // Доступ для всех
    ]);
});