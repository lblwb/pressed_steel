<?php

add_action( 'phpmailer_init', 'setup_smtp_mailer' );
function setup_smtp_mailer( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'mail.nic.ru'; // SMTP-сервер (например smtp.yandex.ru, smtp.gmail.com и т.п.)
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 465;           // 465 для SSL или 587 для TLS
    $phpmailer->Username   = 'site@pressedsteel.ru'; // твой email
    $phpmailer->Password   = '0hUtXezcWYZzU0l';      // Пароль от SMTP
    $phpmailer->SMTPSecure = 'ssl';         // либо 'tls' для порта 587
    $phpmailer->From       = 'site@pressedsteel.ru';
    $phpmailer->FromName   = 'Pressed Steel [SITE]';       // Имя отправителя
}

// Register AJAX hooks for both logged-in and non-logged-in users
add_action('wp_ajax_pressedsteel_send_form', 'pressedsteel_handle_form');
add_action('wp_ajax_nopriv_pressedsteel_send_form', 'pressedsteel_handle_form');

function pressedsteel_handle_form() {
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

    // Email headers
    $headers = [
        'From: Pressed Steel Site <site@pressedsteel.ru>',
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
    $to = 'site@pressedsteel.ru';
    $sent = wp_mail($to, $subject, $body, $headers, $attachments);

    if ($sent) {
        wp_send_json_success('Письмо отправлено');
    } else {
        $error = error_get_last();
        wp_send_json_error('Не удалось отправить письмо: ' . ($error['message'] ?? 'Неизвестная ошибка'));
    }

    wp_die();
}
?>