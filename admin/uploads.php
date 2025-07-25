<?php
    //Устанавливаем кодировку и вывод всех ошибок
    header('Content-Type: text/html; charset=UTF-8');
    
    $dir = $_GET['dir'];
    //Каталог загрузки картинок
    $uploadDir = '../img/'.$dir;
    
    //Вывод ошибок
    $err = array();
    
    //Коды ошибок загрузки файла
    $errUpload = array( 
                         0 => 'Ошибок не возникло, файл был успешно загружен на сервер. ', 
                         1 => 'Размер принятого файла превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini.', 
                         2 => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме.', 
                         3 => 'Загружаемый файл был получен только частично.', 
                         4 => 'Файл не был загружен.', 
                         6 => 'Отсутствует временная папка. Добавлено в PHP 4.3.10 и PHP 5.0.3.' 
                      ); 
                      
    //Определяем типы файлов для загрузки
    $fileTypes = array(
                         'jpg' => 'image/jpeg',
                         'png' => 'image/png',
                         'gif' => 'image/gif'
                        );
                        
        //Проверяем пустые данные или нет
        if(!empty($_FILES))
        {
            //Проверяем на ошибки
            if($_FILES['stringKeyToGetValue']['error'] > 0)
                $err[] = $errUpload[$_FILES['stringKeyToGetValue']['error']];
            
            //Если нет ошибок то грузим файл
            if(empty($err))
            {
                $name = $uploadDir .'/'. $_FILES['stringKeyToGetValue']['name'];
                move_uploaded_file($_FILES['stringKeyToGetValue']['tmp_name'], $name);
                echo 'Файл успешно загружен!';
            }
            else
                echo implode('<br>', $err);
        }

    

?>