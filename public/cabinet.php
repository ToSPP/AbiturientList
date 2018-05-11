<?php

require_once '../src/init.php';
require_once '../views/header.html';

if (isset($_COOKIE['Abiturient'])) {
    
    if (!$personalData = $abiturient->showOne($_COOKIE['Abiturient'])) {
        setcookie('Abiturient', '', time()-3600);
        header('Location: http://localhost/index.php');
    }
    $personalData = array_slice($personalData, 0, 8, true);
   
    foreach ($personalData[0] as $key => $value) {
        $$key = $value;  
    }

} else {
    $name = $surname = $gender = $groupNumber = 
        $email = $sumUSE = $yearOfBirth = $location = '';

    $errorClasses = ['name' => '',
                 'surname' => '',
                 'gender' => '',
                 'groupNumber' => '',
                 'email' => '',
                 'sumUSE' => '',
                 'yearOfBirth' => '',
                 'location' => ''];    
}

require_once '../views/registrationForm.html';
require_once '../views/footer.html';