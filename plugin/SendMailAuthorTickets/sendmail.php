<?php
/**
 * User: ramon149
 * Plugin ModX
 * Send message:
 * - admin/manager about create new ticket
 * - user about publich ticket
 */

switch ($modx->event->name) {

    /* Отправка сообщения админу/менеджеру о создании нового неопубликованного тикета */
    case 'OnDocFormSave':
        if ($mode == 'new' && $resource->class_key == "Ticket" && !$resource->published) {

                // Получаем id автора
                $user_id = $resource->get('createdby');
                // Название тикета
                $ticket_title = $resource->get('pagetitle');

                // Получаем объект modUser
                if ($user = $modx->getObject('modUser', $user_id)){

                    // Получаем связанный с ним профиль пользователя
                    if($profile = $user->getOne('Profile')){

                        $user_fullname = $profile->get('fullname');

                        $modx->setPlaceholders(array(
                            'ticket_title' => $ticket_title,
                            'user_fullname' => $user_fullname,
                        ));

                        $message = $modx->getChunk('chank_name');
                        $modx->getService('mail', 'mail.modPHPMailer');
                        $modx->mail->set(modMail::MAIL_BODY, $message);
                        $modx->mail->set(modMail::MAIL_FROM,'email_in_FROM');
                        $modx->mail->set(modMail::MAIL_FROM_NAME,'from_real_name');
                        $modx->mail->set(modMail::MAIL_SUBJECT,'subject');
                        $modx->mail->address('to', 'email_in_TO');
                        $modx->mail->setHTML(true);

                        if (!$modx->mail->send()) {
                            $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                        }

                        $modx->mail->reset();
                    }
                }
        }
        break;
    /* Отправка сообщения о публикации тикета */
    case 'OnDocPublished':
        if ($resource->class_key == "Ticket" && $resource->published) {

            // Получаем id автора
            $user_id = $resource->get('createdby');

            $ticket_title = $resource->get('pagetitle');
            $ticket_url = $resource->get('uri');

            // Получаем объект modUser
            if ($user = $modx->getObject('modUser', $user_id)){

                // Получаем связанный с ним профиль пользователя
                if($profile = $user->getOne('Profile')){

                    $user_email = $profile->get('email');
                    $user_fullname = $profile->get('fullname');

                    $modx->setPlaceholders(array(
                        'user_fullname' => $user_fullname,
                        'user_email' => $user_email ,
                        'ticket_title' => $ticket_title ,
                        'ticket_url' => $ticket_url ,
                    ));

                    $message = $modx->getChunk('chank_name');
                    $modx->getService('mail', 'mail.modPHPMailer');
                    $modx->mail->set(modMail::MAIL_BODY, $message);
                    $modx->mail->set(modMail::MAIL_FROM,'email_in_FROM');
                    $modx->mail->set(modMail::MAIL_FROM_NAME,'from_real_name');
                    $modx->mail->set(modMail::MAIL_SUBJECT,'subject');
                    $modx->mail->address('to', $user_email);
                    $modx->mail->setHTML(true);

                    if (!$modx->mail->send()) {
                        $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                    }

                    $modx->mail->reset();
                }
            }
        }
        break;
}
