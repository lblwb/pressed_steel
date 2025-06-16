<?php

//add_action('phpmailer_init', 'setup_smtp_mailer');
//function setup_smtp_mailer($phpmailer)
//{
//    $phpmailer->isSMTP();
//    $phpmailer->Host = 'mail.nic.ru'; // SMTP-сервер (например smtp.yandex.ru, smtp.gmail.com и т.п.)
//    $phpmailer->SMTPAuth = true;
//    $phpmailer->Port = 465;           // 465 для SSL или 587 для TLS
//    $phpmailer->Username = 'site@pressedsteel.ru'; // твой email
//    $phpmailer->Password = '0hUtXezcWYZzU0l';      // Пароль от SMTP
//    $phpmailer->SMTPSecure = 'ssl';         // либо 'tls' для порта 587
//    $phpmailer->From = 'site@pressedsteel.ru';
//    $phpmailer->FromName = 'Pressed Steel [SITE]';       // Имя отправителя
//}

//
class PressedSteelOrderHandler
{

    private string $admin_email = 'info@pressedsteel.ru';
    private string $from_email = 'site@pressedsteel.ru';
    private string $from_name = 'Pressed Steel Site';
    private string $info_email = 'info@pressedsteel.ru';

    /**
     * Обработчик REST-запроса
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function handle_request(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $params = $request->get_json_params();

            $form = $params['form'] ?? [];
            $cartItems = $params['cartItems'] ?? [];

            if (empty($cartItems)) {
                return $this->error_response('Корзина пуста', 400);
            }

            $data = [
                'org' => sanitize_text_field($form['org'] ?? ''),
                'inn' => sanitize_text_field($form['inn'] ?? ''),
                'fio' => sanitize_text_field($form['fio'] ?? ''),
                'phone' => sanitize_text_field($form['phone'] ?? ''),
                'email' => sanitize_email($form['email'] ?? ''),
                'cartItems' => $cartItems,
                'order_date' => date('d.m.Y H:i'),
            ];

//            return var_dump($this->admin_email);

//            return;

            // Письмо клиенту
            if (!empty($data['email'])) {
                $client_subject = 'Ваш заказ на pressedsteel.ru';
                $client_message = $this->build_email_message($data, false);
                $this->send_email(esc_html($data['email']), $client_subject, $client_message, $this->admin_email);
            }

            // Письмо администратору
            $admin_subject = '🛒 Новый заказ с сайта: [pressedsteel.ru]';
            $admin_message = $this->build_email_message($data, true);
            $admin_sent = $this->send_email(esc_html($this->admin_email), $admin_subject, $admin_message, esc_html($data['email']));
            if (!$admin_sent) {
                return $this->error_response('Не удалось отправить!', 500);
            }

            return $this->success_response('Заказ успешно отправлен!');
        } catch (Exception $e) {
            return $this->error_response('Ошибка сервера: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Формирует тело письма
     *
     * @param array $data
     * @param bool $is_admin
     * @return string
     */
    private function build_email_message(array $data, bool $is_admin): string
    {
        $fio = esc_html($data['fio']);
        $org = esc_html($data['org']);
        $inn = esc_html($data['inn']);
        $phone = esc_html($data['phone']);
        $email = esc_html($data['email']);
        $cartItems = $data['cartItems'];
        $order_date = esc_html($data['order_date']);

        $message = '';

        if ($is_admin) {
            $message .= '<h4>🛒 Новый заказ с сайта: [pressedsteel.ru]</h4>';
            $message .= '<hr>';
            $message .= '<p><strong>Форма:</strong> ЗАКАЗА</p>';
            $message .= '<hr><br>';
            $message .= '<h4>👤 Данные заказчика:</h4>';
        } else {
            $message .= '<h4>Ваши данные:</h4>';
        }

        $message .= '<ul>';
        $message .= "<li><strong>ФИО:</strong> {$fio}</li>";
        $message .= "<li><strong>Организация:</strong> {$org}</li>";
        $message .= "<li><strong>ИНН:</strong> {$inn}</li>";
        $message .= "<li><strong>Телефон:</strong> {$phone}</li>";
        $message .= "<li><strong>Email:</strong> {$email}</li>";
        $message .= '</ul>';
        $message .= '<hr><br>';


        $message .= '<h4>📦 Состав заказа:</h4>';
        $message .= '<table style="width:100%; border-collapse: collapse; font-family: Arial, sans-serif;">';
        $message .= '<thead>';
        $message .= '<tr style="background-color:#f4f4f4; text-align:left;">';
        $message .= '<th style="border:1px solid #ddd; padding:8px;">Название товара</th>';
        $message .= '<th style="border:1px solid #ddd; padding:8px;">Параметры</th>';
        $message .= '<th style="border:1px solid #ddd; padding:8px;">Кол-во</th>';
        $message .= '<th style="border:1px solid #ddd; padding:8px;">Ссылка</th>';
        $message .= '</tr>';
        $message .= '</thead><tbody>';

        foreach ($cartItems as $item) {
            $name = sanitize_text_field($item['name'] ?? '—');
            $quantity = absint($item['quantity'] ?? 0);

            // Формируем параметры
            $attributes = '';
            if (!empty($item['attributes']) && is_array($item['attributes'])) {
                foreach ($item['attributes'] as $key => $value) {
                    $attributes .= esc_html($key) . ': ' . esc_html($value) . '<br>';
                }
            }

            // Формируем ссылку (если есть)
            $link = '#';
            if (!empty($item['product_id'])) {
//                var_dump($item);
                $link = get_permalink(intval(base64_decode($item['product_id'])));
            }

            $message .= '<tr>';
            $message .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($name) . '</td>';
            $message .= '<td style="border:1px solid #ddd; padding:8px;">' . $attributes . '</td>';
            $message .= '<td style="border:1px solid #ddd; padding:8px;">' . $quantity . '</td>';
            $message .= '<td style="border:1px solid #ddd; padding:8px;"><a href="' . esc_url($link) . '" target="_blank">Открыть товар</a></td>';
            $message .= '</tr>';
        }

        $message .= '</tbody></table>';
        $message .= '<hr><br>';

        if (!$is_admin) {
            $message .= '<p>Вы можете ускорить наше сотрудничество, заполнив приложенную форму и отправив ее нам по адресу: <a href="mailto:' . esc_html($this->info_email) . '">' . esc_html($this->info_email) . '</a></p>';
            $message .= '<hr><br>';
        }

        $message .= '<p><strong>Дата заказа:</strong> ' . $order_date . '</p>';
        return $message;
    }

    /**
     * Отправка письма
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string|null $reply_to
     * @return bool
     */
    private function send_email(string $to, string $subject, string $message, ?string $reply_to = null): bool
    {
        $headers = [
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Content-Type: text/html; charset=UTF-8',
        ];

        if ($reply_to) {
            $headers[] = 'Reply-To: ' . sanitize_email($reply_to);
        }


//        wp_log()
        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Формируем успешный ответ REST API
     *
     * @param string $message
     * @return WP_REST_Response
     */
    private function success_response(string $message): WP_REST_Response
    {
        return new WP_REST_Response([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    /**
     * Формируем ошибочный ответ REST API
     *
     * @param string $message
     * @param int $code
     * @return WP_REST_Response
     */
    private function error_response(string $message, int $code = 400): WP_REST_Response
    {
        return new WP_REST_Response([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}