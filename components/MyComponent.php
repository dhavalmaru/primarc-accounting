<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class MyComponent extends Component
{
	function format_money($number, $decimal=2){
        if(!isset($number)) $number=0;

        $negative=false;
        if(strpos($number, '-')!==false){
            $negative=true;
            $number = str_replace('-', '', $number);
        }

        $number = floatval(str_replace(',', '', $number));
        $number = round($number, $decimal);

        $decimal_digits="";
        
        if(strpos($number, '.')!==false){
            $decimal_digits = substr($number, strpos($number, '.'));
            $number = substr($number, 0, strpos($number, '.'));
        } else {
            $decimal_digits=".";
        }
        
        if($decimal>0){
            $decimal_digits = str_pad($decimal_digits, $decimal+1, "0", STR_PAD_RIGHT);
            // echo $decimal . '<br/>';
            // echo $number . '<br/>';
        } else {
            $decimal_digits = '';
        }
        
        $len = strlen($number);
        $m = '';
        $number = strrev($number);
        for($i=0;$i<$len;$i++){
            if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$len){
                $m .=',';
            }
            $m .=$number[$i];
        }

        $number = strrev($m);
        $number = $number . $decimal_digits;

        if($negative==true){
            $number = '-' . $number;
        }

        return $number;
    }

    function format_number($number, $decimal=2){
        if(!isset($number)) $number=0;
        $number = floatval(str_replace(',', '', $number));
        $number = round($number, $decimal);
        return $number;
    }
    
    // function validateDate($date, $format = 'd/m/Y') {
    //     $d = DateTime::createFromFormat($format, $date);
    //     return $d && $d->format($format) == $date;
    // }

    function FormatDate($date, $format = 'd/m/Y') {
        $dateTime = \DateTime::createFromFormat($format, $date);
        // \Yii::$app->formatter->asDatetime($dateTime, 'php:Y-m-d');
        $dateTime = $dateTime->format('Y-m-d');

        // $d = DateTime::createFromFormat($format, $date);
        // $returnDate = null;
        // if ($d && $d->format($format) == $date) {
        //     // $returnDate = DateTime::createFromFormat($format, $date)->format('Y-m-d');
        //     $dateInput = explode('/',$date);
        //     $returnDate = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        // }

        return $dateTime;
    }

    function get_address($address, $landmark, $city, $pincode, $state, $country) {
        if(isset($address)) {
            $address = $address . ', ';
        }
        if(isset($landmark)) {
            $address = $address . $landmark . ', ';
        }
        if(isset($city)) {
            $address = $address . $city . ' ';
        }
        if(isset($pincode)) {
            $address = $address . $pincode . ' ';
        }
        if(isset($state)) {
            $address = $address . $state . ', ';
        }
        if(isset($country)) {
            $address = $address . $country . ',';
        }

        $address = str_replace(', , , , ,', ',', $address);
        $address = str_replace(', , , ,', ',', $address);
        $address = str_replace(', , ,', ',', $address);
        $address = str_replace(', ,', ',', $address);

        if(strpos($address, ',')!==false){
            $address = substr($address, 0, strlen($address)-1);
        }

        return $address;
    }

    function convert_number_to_words($number) {
        $words = array('0' => ' ', '1' => 'One', '2' => 'Two',
        '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
        '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
        '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
        '13' => 'Thirteen', '14' => 'Fourteen',
        '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
        '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
        '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
        '60' => 'Sixty', '70' => 'Seventy',
        '80' => 'Eighty', '90' => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');

        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $rupees_text = $result;

        // $points = ($point) ? (" and " . $words[$point / 10] . " " .  $words[$point = $point % 10]) : '';
        $hundred = null;
        $digits_1 = strlen($point);
        $i = 0;
        $str = array();
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($point % $divider);
            $point = floor($point / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $paise_text = $result;

        // if($points==""){
        //     $result = $result . "Rupees ";
        // } else {
        //     $result = $result . "Rupees " . $points . " Paise";
        // }

        if($paise_text==""){
            $result = $rupees_text . "Rupees Only";
        } else {
            $result = $rupees_text . "Rupees and " . $paise_text . " Paise Only";
        }
        return $result;
    }

    function get_financial_year($date){
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        $from = $dateTime->format('Y');
        $to = $dateTime->format('Y');
        if (date('m') > 3) {
            $to = (int)($dateTime->format('Y')) +1;
        } else {
            $from = (int)($dateTime->format('Y')) -1;
        }
        $year = $from . '-' . substr($to, 2);
        return $year;
    }
}