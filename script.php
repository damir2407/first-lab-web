<?php
@session_start();
$xVal = $_GET['xValue'];
$yVal = $_GET['yValue'];
$rVal = $_GET['rValue'];
$hit = '';


if (!isset($_SESSION["tableRows"])) $_SESSION["tableRows"] = array();

// засекаем время начала работы скрипта
$start = getMicrotime();
// возвращает для удобства метку времени
function getMicrotime()
{
    return microtime(true);
}

// текущее время
$date_utc = new DateTime("now", new DateTimeZone("+0300"));


// Для отображение попаданий
function transformForHit($xVal, $yVal, $rVal)
{
    if ((checkHitTriangle($xVal, $yVal, $rVal) == '1') || (checkHitSquare($xVal, $yVal, $rVal) == '1') || (checkHitCircle($xVal, $yVal, $rVal) == '1')) {
        $hit = 'true';
    } else $hit = 'false';
    return $hit;
}


// Проверка на входимость в квадрат
function checkHitSquare($xVal, $yVal, $rVal)
{
    return (($xVal <= 0 && $xVal >= -$rVal) && ($yVal <= 0 && $yVal >= -$rVal));
}



// Проверка на входимость в треугольник (векторное произведение)
function checkHitTriangle($xVal, $yVal, $rVal)
{
    $first = (-$xVal) * (-$rVal / 2);
    $second = (-$xVal) * ($rVal / 2) - ($rVal / 2) * (-$rVal / 2 - $yVal);
    $third = - ($rVal / 2) * (-$yVal);
    if (($first >= 0 && $second >= 0 && $third >= 0) || ($first <= 0 && $second <= 0 && $third <= 0)) {
        return 1;
    } else return 0;
}


// Проверка на входимость в полуокружность
function checkHitCircle($xVal, $yVal, $rVal)
{
    return $xVal <= 0 && $yVal >= 0 &&
        sqrt($xVal * $xVal + $yVal * $yVal) <= $rVal;
}


// Валидация значений X
function validateX($xVal)
{
    if (!isset($xVal)) {
        return false;
    }

    return is_numeric($xVal) && $xVal > -3 && $xVal < 3 && strlen((string)$xVal)<=10;
}



// Валидация значений Y
function validateY($yVal)
{
    if (!isset($yVal)) {
        return false;
    }

    return (is_numeric($yVal) && ($yVal == -5 || $yVal == -4 || $yVal == -3 || $yVal == -2 || $yVal == -1 || $yVal == 0 || $yVal == 1 || $yVal == 2 || $yVal == 3));
}


// Валидация значений R
function validateR($rVal)
{
    if (!isset($rVal)) {
        return false;
    }
    return (is_numeric($rVal) && ($rVal == 1 || $rVal == 2 || $rVal == 3 || $rVal == 4 || $rVal == 5));
}

// Отправка результата 
for ($i = 0; $i < count($yVal); $i++) {
    $newRow;
    if (validateX($xVal) && validateY($yVal[$i]) && validateR($rVal) == 1 && $_SERVER['REQUEST_METHOD']=="GET") {
        $hit = transformForHit($xVal, $yVal[$i], $rVal);
        $thisYVal = $yVal[$i];
        $microTime = getMicrotime();
        $nowTime = $date_utc->format(DateTime::RFC850);
        array_push($_SESSION["tableRows"], "<tr>
            <td>$xVal</td>
            <td>$thisYVal</td>
            <td>$rVal</td>
            <td>$hit</td>
            <td>$microTime</td>
            <td>$nowTime</td>
    ");
    } else{
        header("404 Not Found", true, 400);
    }
}
echo "<table id='result-table'>
<tr>
    <th class='coords-col'>X</th>
    <th class='coords-col'>Y</th>
    <th class='coords-col'>R</th>
    <th>Попадание</th>
    <th>Время работы скрипта</th>
    <th>Текущее время</th>
</tr>";
foreach ($_SESSION["tableRows"] as $tableRow) echo $tableRow;
echo "</table>";
