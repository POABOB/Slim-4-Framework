<?php

declare(strict_types=1);

namespace App\Utils;

class Validation
{
  private $_errors = array();
  public function validate($src, $rules = array() ){
    foreach($src as $item => $item_value){
      if(key_exists($item, $rules)){
        foreach($rules[$item] as $rule => $rule_value){
          if(is_int($rule)) {
            $rule = $rule_value;
          }
          switch ($rule){
            case 'required':
              if(empty($item_value) && $rule_value){
                //empty缺點 0 false會返回true
                if($item_value === 0 || $item_value === false || $item_value === '0' || $item_value === 0.00) {
                  break;
                }
                $this->addError($item,ucwords($item). ' 無法為空');
              }
              break;

            case 'minLen':
              if(mb_strlen((string)$item_value) < $rule_value){
                $this->addError($item, ucwords($item). ' 最小長度應為 '.$rule_value. ' 個字元');
              }
              break;

            case 'maxLen':
              if(mb_strlen((string)$item_value) > $rule_value){
                $this->addError($item, ucwords($item). ' 最大長度應為 '.$rule_value. ' 個字元');
              }
              break;

            case 'numeric':
              if(!is_numeric($item_value) && $rule_value){
                $this->addError($item, ucwords($item). ' 應為數字');
              }
              break;
            case 'float':
              if(!is_float($item_value) && $rule_value){
                $this->addError($item, ucwords($item). ' 應為浮點數');
              }
              break;
            case 'alpha':
              if(!ctype_alpha($item_value) && $rule_value){
                $this->addError($item, ucwords($item). ' 應為字母');
              }
              break;
            case 'space':
              if(!ctype_space($item_value) && $rule_value){
                $this->addError($item, ucwords($item). ' 不應有空格');
              }
              break;
            case 'email':
              if(!filter_var($item_value, FILTER_VALIDATE_EMAIL) && $rule_value){
                $this->addError($item, ucwords($item). ' 不為Email格式');
              }
              break;
            case 'same':
              if($item_value != $rule_value && $rule_value){
                $this->addError($item, ucwords($item). ' 輸入要一致');
              }
              break;
            case 'id_number':
              $map = array(
                'A'=>10,'B'=>11,'C'=>12,'D'=>13,'E'=>14,'F'=>15,
                'G'=>16,'H'=>17,'I'=>34,'J'=>18,'K'=>19,'L'=>20,
                'M'=>21,'N'=>22,'O'=>35,'P'=>23,'Q'=>24,'R'=>25,
                'S'=>26,'T'=>27,'U'=>28,'V'=>29,'W'=>32,'X'=>30,
                'Y'=>31,'Z'=>33
              );
              // ^: 必須以英文開頭
              // $: 必須以數字結尾
              // 先檢查字數可以節省時間
              $strLen = strlen($item_value);
              $item_value = strtoupper($item_value);
              if (($strLen != 10 || preg_match("/^[A-Z][1-2][0-9]+$/", $item_value) == 0) && $rule_value) {
                $this->addError($item, ucwords($item). ' 不為身份證格式');
                break;
              }
              $code = 0;
              for($i = 0; $i < $strLen; $i++){
                $symbol = substr($item_value,$i,1);
                // 英文字母
                if($i == 0){
                  $tmp = $map[$symbol];
                  $code += intval($tmp/10) + ($tmp%10)*9;
                // 最後一碼
                }else if($i == $strLen - 1){
                  $code += intval($symbol);
                // 其他: 乘上 8,7,6,5,4,3,2,1
                }else{
                  $code += intval($symbol) * (9 - $i);
                }
              }
              if($code % 10 != 0 && $rule_value){
                $this->addError($item, ucwords($item). ' 不為身份證格式');
              }
              break;
          }
        }
      }
    }    
  }
  private function addError($item, $error){
    $this->_errors[$item][] = $error;
  }
  public function error(){
    if(empty($this->_errors)) {
      return false;
    }
    return $this->_errors;
  }
}