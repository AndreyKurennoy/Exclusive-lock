<?php
$header = "Content-Type: text/plain; charset = utf-8\r\n";
$header .= "From: ";
$email1 = "19ofis96@mail.ru";
if(!empty($_POST)){
    $errors = [];
    $name = strip_tags($_POST['name']);
    $email = strip_tags($_POST['email']);
    $comment = strip_tags($_POST['comment']);

    $savedFile = 'comments.txt';

    if (empty($name) === true || empty($email) === true || empty($comment) === true){
        $errors[] = "Не все поля заполнены!";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Недействительный email адрес";
        }


        if (empty($errors) === true) {
            $arr = [
                'name' => strip_tags(trim($name)),
                'email' => strip_tags(trim($email)),
                'comment' => strip_tags(trim($comment)),
            ];
            $data = serialize($arr) . PHP_EOL;
            $fp = fopen($savedFile, 'a');
            $f=file_get_contents('comments.txt');

            flock ($fp,LOCK_EX);//блокировка файла
            ftruncate($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
            fputs($fp ,"$data");//работа с файлом
            fputs($fp ,"$f");//работа с файлом
            fflush($fp);
            flock ($fp,LOCK_UN);//снятие блокировки
            fclose($fp);
//            if (fwrite($fp, $data) === false) {
//                echo "Ваш комментарий не сохранен";
//                exit();
//            }

            header('Location: comments.php?saved');
        }
     }
    }

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">

    <?php
    if(isset($_GET['saved'])){
        echo "<div class='alert alert-success'>Комментарий сохранен!</div>";
    } else {


    if (empty($errors) === false) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>', $error, '</li>';
        }
        echo '</ul>';
    }
    ?>

    <h1>Форма</h1>
    <form action="" method="post" novalidate>
        <div class="form-group">
            <label for="name">Имя: </label>
            <input type="text" class="form-control" id="name" name="name"
                <?php if (isset($_POST['name'])) echo "value='" . $_POST['name'] . "'"; ?> placeholder="Ваше имя">
        </div>

        <div class="form-group">
            <label for="email">Email: </label>
            <input type="email" class="form-control" id="email" name="email"
                   <?php if (isset($_POST['email'])) echo "value='" . $_POST['email'] . "'"; ?>placeholder="Ваш Email">
        </div>

        <div class="form-group">
            <label for="comment">Сообщение: </label>
            <textarea class="form-control" rows="10" id="comment" name="comment">
                <?php if (isset($_POST['comment'])) echo $_POST['comment']; ?>
            </textarea>
            <div class="form-group">
            <button type="submit" class="btn btn-primary">Отправить</button>
                </div>
        </div>
        </form>

        <?php
        }
        $content = file('comments.txt', FILE_IGNORE_NEW_LINES);

        foreach($content as $comment){
            $comments = unserialize($comment);
            echo '<div class="panel panel-primary">';
                echo '<div class="panel-heading">' . $comments['name'] . '<span class="pull-right">' .$comments['email'] . '</span>' . '</div>';
                echo '<div class="panel-body">' . $comments['comment'];
                    ;
                echo '</div>';
            echo '</div>';
        }
        ?>
</div>






</body>
</html>