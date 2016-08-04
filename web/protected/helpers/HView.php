<?php
class HView {
  
  /**
   * Remove acentos de string
   * @param type $str
   * @return type
   */
  public static function removeAcentos($str) {
      // assume $str esteja em UTF-8
      $map = array(
          'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o',
          'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A',
          'Â' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U',
          'Ü' => 'U', 'Ç' => 'C'
      );
      return strtr($str, $map);
  }
  
  /**
   * Normaliza string para busca
   * @param type $str
   * @return type
   */
  public static function dirName($str) {
    return str_replace("'", '', preg_replace('/[^a-zA-Z0-9\']/', '_', $str));
  }



}