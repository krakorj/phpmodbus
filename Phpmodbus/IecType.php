<?php
/**
 * Phpmodbus Copyright (c) 2004, 2008 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)
 *  
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt. 
 * 
 * @author Jan Krakora
 * @copyright Copyright (c) 2004, 2008 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)
 * @license PhpModbus license 
 * @category Phpmodbus
 * @package Phpmodbus
 * @version $id$
 */

/**
 * IecType class
 *   
 * The class includes set of IEC-1131 data type functions that converts a PHP 
 * data types into a IEC format and vice versa.
 *
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2008 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)      
 * @package Phpmodbus  
 */
class IecType {

  /**
   * iecBYTE
   *  
   * Converts a value to IEC-1131 BYTE data type
   * 
   * @param value value from 0 to 255
   * @return value IEC BYTE data type
   *  
   */   
  function iecBYTE($value){
    return chr($value & 0xFF);
  }
  
  /**
   * iecINT
   *  
   * Converts a value to IEC-1131 INT data type
   * 
   * @param value value to be converted
   * @return value IEC-1131 INT data type    
   *  
   */ 
  function iecINT($value){
    return self::iecBYTE(($value >> 8) & 0x00FF) . 
      self::iecBYTE(($value & 0x00FF));
  }
  
  /**
   * iecDINT
   *  
   * Converts a value to IEC-1131 DINT data type
   * 
   * @param value value to be converted
   * @param value endianness defines endian codding (little endian == 0, big endian == 1)  
   * @return value IEC-1131 INT data type
   *  
   */
  function iecDINT($value, $endianness = 0){  
    if ($endianness == 0)
      return
        self::iecBYTE(($value & 0x000000FF)) .
        self::iecBYTE(($value >> 8) & 0x000000FF) .
        self::iecBYTE(($value >> 24) & 0x000000FF) .
        self::iecBYTE(($value >> 16) & 0x000000FF);
    else
      return
        self::iecBYTE(($value >> 24) & 0x000000FF) .
        self::iecBYTE(($value >> 16) & 0x000000FF) .
        self::iecBYTE(($value >> 8) & 0x000000FF) .
        self::iecBYTE(($value & 0x000000FF));
  }
  
  /**
   * iecREAL
   *  
   * Converts a value to IEC-1131 REAL data type. The function uses @use function float2iecReal. 
   * 
   * @param value value to be converted
   * @param value endianness defines endian codding (little endian == 0, big endian == 1) 
   * @return value IEC-1131 REAL data type
   */
  function iecREAL($value, $endianness = 0){
    $real = self::float2iecReal($value);
    if ($endianness == 0)
      return
        self::iecBYTE(($real & 0x000000FF)) .
        self::iecBYTE(($real >> 8) & 0x000000FF) .
        self::iecBYTE(($real >> 24) & 0x000000FF) .
        self::iecBYTE(($real >> 16) & 0x000000FF);
    else
      return
        self::iecBYTE(($real >> 24) & 0x000000FF) .
        self::iecBYTE(($real >> 16) & 0x000000FF) .
        self::iecBYTE(($real >> 8) & 0x000000FF) .
        self::iecBYTE(($real & 0x000000FF));
  }
  
  /**
   * float2iecReal
   *  
   * This function converts float value to IEC-1131 REAL single precision form.
   * 
   * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
   * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes @ Georgia Tech 21-Nov-2007]*
   *    
   * @param float value to be converted
   * @return value IEC REAL data type 
   */   
  function float2iecReal($value){
    $bias = 128;
  	$cnst = 281;		// 1 (carry bit) + 127 + 1 + 126 + 24 + 2 (round bits)
  	$two_power_x = array(1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 
      4096, 8192, 16384, 32768, 65536, 131072, 262144, 524288, 1048576, 
      2097152, 4194304);    
    //convert and seperate input to integer and decimal parts
    $val = abs($value);
    $intpart = floor($val);
    $decpart = $val - $intpart;  
    //convert integer part
  	for ($i=0;$i<$cnst;$i++) $real_significand_bin[$i] = 0;
    $i = $bias;
    while ((($intpart / 2) != 0) && ($i >= 0))
    {
      $real_significand_bin[$i] = $intpart % 2;
      if (($intpart % 2) == 0) $intpart = $intpart / 2;
        else $intpart = $intpart / 2 - 0.5;
      $i -= 1;
    }  
    //convert decimal part
    $i = $bias+1;
    while (($decpart > 0) && ($i < $cnst))
    {
      $decpart *= 2;
      if ($decpart >= 1) {
        $real_significand_bin[$i] = 1;
        $decpart --;
        $i++;
      }
      else 
      {
        $real_significand_bin[i] = 0;
        $i++;
      }
    }  
    //obtain exponent value
    $i = 0;  
    //find most significant bit of significand
    while (($i < $cnst) && ($real_significand_bin[$i] != 1)) $i++;
    //
  	$index_exp = $i;
    $real_exponent = 128 - $index_exp;
  	if ($real_exponent < -126) return 0;
  	if (($real_exponent > 127)&&($real_float>0)) return 0x7F7FFFFF;
  	if (($real_exponent > 127)&&($real_float<0)) return 0xFF7FFFFF;
  	for ($i=0; $i<23; $i++)
  		$real_significand = $real_significand + $real_significand_bin[$index_exp+1+$i] * $two_power_x[22-$i];
  	// return
  	if ($value<0) $w = 0x80000000 + ($real_significand & 0x7FFFFF) + ((($real_exponent+127)<<23) & 0x7F800000);
  	else $w = ($real_significand & 0x7FFFFF) + ((($real_exponent+127)<<23) & 0x7F800000);
  	return $w;
  }
  
  /**
   * iecReal2float
   *  
   * This function converts a value in IEC-1131 REAL single precision form to float.
   *  
   * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
   * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes @ Georgia Tech 21-Nov-2007]
   * 
   * @param value value in IEC REAL data type to be converted
   * @return float float value 
   */
  function iecReal2float($value){
    $two_pow_minus_x = array(
      1, 0.5, 0.25, 0.125, 0.0625, 0.03125, 0.015625, 
      0.0078125, 0.00390625, 0.001953125, 0.0009765625, 
      0.00048828125, 0.000244140625, 0.0001220703125, 
      0.00006103515625,	0.000030517578125, 0.0000152587890625, 
      0.00000762939453125, 0.000003814697265625, 0.0000019073486328125, 
      0.00000095367431640625, 0.000000476837158203125,
  		0.0000002384185791015625, 0.00000011920928955078125);
    // get sign, mantisa, exponent
  	$real_mantisa = $value & 0x7FFFFF | 0x800000; 
  	$real_exponent = ($value>>23) & 0xFF;
  	$real_sign = ($value>>31) & 0x01;
  	$bin_exponent = $real_exponent - 127;
  	// decode value
  	if (( $bin_exponent >= -126) && ($bin_exponent <= 127)) {
      // Mantissa decoding	
  		for ($i=0; $i<24; $i++) {		  
  		  if ($real_mantisa & 0x01)
  			  $val += $two_pow_minus_x[23-$i];
  			$real_mantisa = $real_mantisa >> 1;
  		}
      // Base
  		$val = $val * pow(2,$bin_exponent);
  		if (($real_sign == 1)) $val = -$val;
  	}	
  	return (float)$val;
  }
  
}
  
?>