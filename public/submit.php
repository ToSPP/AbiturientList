<?php

require_once '../src/init.php';
require_once '../views/header.html';

if (isset($_POST)) {
    $errors = $validator->validateSet($_POST);
    $foundID = $abiturient->findEmail($_POST['email']);
    if (isset($_GET['id'])) {
        if ($foundID && intval($_GET['id']) === intval($foundID[0][0])) {
            unset($errors['email']);          
        }
    }
}

if ($errors) {
    foreach ($errors as $key => $error) {
        $errorClasses[$key] = 'alert alert-danger';
        $errorMessage[$key] = '';
        foreach ($error as $type => $value) {
            switch ($type) {
                case 0:
                    $errorMessage[$key] .= "<p class='alert alert-warning'>"
                        . "Вы превысили допустимый лимит символов. "
                        . "Использовано: $value, "
                        . "допустимо: " . Validator::$accept[$key]['limit'] . ".</p>";
                    break;
                case 1:
                    $errorMessage[$key] .= "<p class='alert alert-warning'>"
                        . "Вы использовали недопустимые символы: "
                        . "'$value', допустимо: '"
                        . Validator::$accept[$key]['symbolToText'] . "'.</p>";
                    break;
                case 2:
                    $errorMessage[$key] .= "<p class='alert alert-warning'>"
                        . "Вы использовали недопустимое значение: "
                        . "'$value', допустимо: '"
                        . Validator::$accept[$key]['valueToText'] . "'.</p>";
                    break;
                case 3:
                    $errorMessage[$key] .= "<p class='alert alert-warning'>"
                        . "Введенное значение имеет неверный формат. "
                        . "Вы ввели: '$value', допустимо: '"
                        . Validator::$accept[$key]['formatToText'] . "'.</p>";
                    break;
                case 4:
                    $errorMessage[$key] .= "<p class='alert alert-warning'>"
                        . "Пользователь с таким электронным "
                        . "адресом уже зарегистрирован.</p>";
                    break;                
            }
        }        
    }
    
    foreach ($_POST as $key => $value) {
        ${$key} = $value;
    }
    $id = $_GET['id'] ?? '';
    require_once '../views/registrationForm.html';
} else {
    foreach ($_POST as $key => $value) {
        $set[] = $value;
    }
    if (isset($_COOKIE['Abiturient']) && isset($_GET['id'])) {
        $set[8] = $_COOKIE['Abiturient'];
        $abiturient->update(intval($_GET['id']), $set);
    } else {
        $lastID = $abiturient->insert($set);
        $value = $abiturient->findCode($lastID);
        setcookie('Abiturient', $value[0][0], time()+315360000);
    }
    
    echo "<p>Спасибо, данные сохранены, при желании, вы можете их "
    . "<a href='../cabinet.php'>отредактировать.</a></p>";
    echo "<p><a href='../index.php'>Список абитуриентов</a></p>";
}

require_once '../views/footer.html';