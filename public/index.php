
<?php
require_once '../src/init.php';
require_once '../views/header.html';

$sorting = [0, 0, 0, 1];
$arrow = '&#8595';
$result = '';
$q = '';

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}

if (isset($_GET['sortBy'])) {
    $dir = (isset($_GET['dir']) ? strval($_GET['dir']) : 'asc');
    if (mb_strtolower($dir) !== 'asc' && mb_strtolower($dir) !== 'desc') {
        $dir = 'asc';
    }
    switch ($_GET['sortBy']) {
        case 'name':
            $col = 'name';
            $sorting[0] = 1;
            $sorting[3] = 0;
            break;
        case 'surname':
            $col = 'surname';
            $sorting[1] = 1;
            $sorting[3] = 0;
            break;
        case 'groupNumber':
            $col = 'groupNumber';
            $sorting[2] = 1;
            $sorting[3] = 0;
            break;
        default:
            $col = 'sumUSE';
            break;
    }
    $arrow = ($dir === 'asc' ? '&#8595' : '&#8593');
}

if (isset($_GET['q']) && isset($_GET['sortBy'])) {
    $q = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
    $result = $abiturient->findWhere(strval(urldecode($_GET['q'])), $col, $dir);
} else if (isset($_GET['q'])) {
    $q = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
    $result = $abiturient->findWhere(strval(urldecode($_GET['q'])));
} else if (isset($_GET['sortBy'])) {
    $result = $abiturient->show($col, $dir);
}

if (!$result) {
    $result = $abiturient->show();
}

if (count($result) > $abiturientPerPage) {
    $limit  = $page * $abiturientPerPage;
    $offset = $limit - $abiturientPerPage;
} else {
    $limit  = count($result);
    $offset = 0;
}

require_once '../views/abiturientList.html';
require_once '../views/footer.html';