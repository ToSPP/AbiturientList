<?php

require_once 'config.php';

spl_autoload_register(function ($class) {
    $path = __DIR__ . '\\' . $class . '.php';
    
    if (file_exists($path)) {
        require_once $class . ".php";    
    }
});

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit ('База данных не доступна: ' . iconv('cp1251', 'utf-8', $e->getMessage()));
}

$abiturient = new AbiturientDataGateway($dbh);
$validator = new Validator($abiturient);
$abiturientPerPage = 5; // количество записей на странице
$title = isset($_GET['q']) ? "Поиск абитуриентов" : "Список абитуриентов";

$errorClasses = ['name'        => '',
                 'surname'     => '',
                 'gender'      => '',
                 'groupNumber' => '',
                 'email'       => '',
                 'sumUSE'      => '',
                 'yearOfBirth' => '',
                 'location'    => '',
                ];