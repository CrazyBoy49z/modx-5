<?php
/** @var modX $modx */
switch ($modx->event->name) {

    /* Отправка сообщения о публикации тикета */
    case 'OnDocPublished':
        if ($resource->class_key == "Ticket") {

            // Получаем id автора
            $user_id = $resource->get('createby');
            
            $ticket_title = $resource->get('pagetitle');
            $ticket_url = $resource->get('uri');

            // Получаем объект modUser
            if ($user = $modx->getObject('modUser', $user_id)){
				
                // Получаем связанный с ним профиль пользователя
                if($profile = $user->getOne('Profile')){
					
                    $user_email = $profile->get('email');
                    $user_fullname = $profile->get('fulname');
                    
                    $modx->setPlaceholders(array(
                       'user_fullname' => $user_fullname,
                       'user_email' => $user_email ,
                       'ticket_title' => $ticket_title ,
                       'ticket_url' => $ticket_url ,
                    ));
                    
                    $message = $modx->getChunk('AddNewMk');
                    $modx->getService('mail', 'mail.modPHPMailer');
                    $modx->mail->set(modMail::MAIL_BODY, $message);
                    $modx->mail->set(modMail::MAIL_FROM,'noreply@master-diy.ru');
                    $modx->mail->set(modMail::MAIL_FROM_NAME,'Сделай сам - Шаг за шагом');
                    $modx->mail->set(modMail::MAIL_SUBJECT,'Мастер-класс опубликован - Сделай сам - Шаг за шагом');
                    $modx->mail->address('to', $email);

                    $modx->mail->setHTML(true);

                    if (!$modx->mail->send()) {
                        $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                    }
                  
                    $modx->mail->reset();
                }
            }
        }
    break;

    /* Отправка сообщения админу/менеджеру о создании нового неопубликованного тикета */
    case 'OnDocFormSave':
        if ($mode == 'new' && $resource->class_key == "Ticket") {
			
            if(!$resource->published){

            // Получаем id автора
            $user_id = $resource->get('createby');
            
            $ticket_title = $resource->get('pagetitle');

            // Получаем объект modUser
            if ($user = $modx->getObject('modUser', $user_id)){
				
                // Получаем связанный с ним профиль пользователя
                if($profile = $user->getOne('Profile')){
                    
                    $modx->setPlaceholders(array(
                       'ticket_title' => $ticket_title,
                    ));
                    
                    $message = $modx->getChunk('AddNewMk');
                    $modx->getService('mail', 'mail.modPHPMailer');
                    $modx->mail->set(modMail::MAIL_BODY, $message);
                    $modx->mail->set(modMail::MAIL_FROM,'noreply@master-diy.ru');
                    $modx->mail->set(modMail::MAIL_FROM_NAME,'Сделай сам - Шаг за шагом');
                    $modx->mail->set(modMail::MAIL_SUBJECT,'Мастер-класс опубликован - Сделай сам - Шаг за шагом');
                    $modx->mail->address('to', 'admin@master-diy.ru');

                    $modx->mail->setHTML(true);

                    if (!$modx->mail->send()) {
                        $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                    }
                  
                    $modx->mail->reset();
                }
            }
            
            }
        }
    break;
}
