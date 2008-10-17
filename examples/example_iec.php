<?php

require_once './Phpmodbus/IecType.php';

// IEC transformation
//
//
echo "<h2>IEC transformation example</h2>";

// test    
$in = 25;
$iecReal = IecType::float2iecReal($in);
$value = IecType::iecReal2float($iecReal);
echo "any input value: " . $in . "</br>";
echo "IEC Real value [hex]: " . dechex($iecReal) . "</br>";
echo "float value: " . $value . "</br>";

echo "</br>";

// test 2
$in = -2;
$iecReal = IecType::float2iecReal($in);
$value = IecType::iecReal2float($iecReal);
echo "any input value: " . $in . "</br>";
echo "IEC Real value [hex]: " . dechex($iecReal) . "</br>";
echo "float value: " . $value . "</br>";

echo "</br>";

// test 3
$in = 1/3;
$iecReal = IecType::float2iecReal($in);
$value = IecType::iecReal2float($iecReal);
echo "any input value: " . $in . "</br>";
echo "IEC Real value [hex]: " . dechex($iecReal) . "</br>";
echo "float value: " . $value . "</br>";
?>