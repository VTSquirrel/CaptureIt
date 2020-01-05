<?php
/**
 * Name String Order
 *
 * @version    2.0 (2017-05-11 10:56:00 GMT)
 * @author     Peter Kahl <peter.kahl@colossalmind.com>
 * @copyright  2017 Peter Kahl
 * @license    Apache License, Version 2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      <http://www.apache.org/licenses/LICENSE-2.0>
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace peterkahl\nameStringOrder;

//use peterkahl\CharsetFromString\CharsetFromString;
use \Exception;

class nameStringOrder {

  /**
   * Version
   * @var string
   */
  const VERSION = '2.0';

  /**
   * First (given) name
   * @var string
   */
  private $first;

  /**
   * Last name (surname)
   * @var string
   */
  private $last;

  /**
   * Middle name
   * @var string
   */
  private $middle;

  /**
   * Predominant character set of the input string
   * @var string
   */
  private $charset;

  #===================================================================

  public function __construct($unordered) {
    $this->first  = '';
    $this->last   = '';
    $this->middle = '';
    if (empty($unordered)) {
      return;
    }
    $this->charset = "LATIN";
    switch ($this->charset) {
      case 'LATIN':
        $this->segmentLatin($unordered);
        return;
      /*case 'CYRILLIC': 
        $this->segmentCyrillic($unordered);
        return;
      case 'CJK':
        $this->segmentCJK($unordered);
        return;*/
      default:
        $this->segmentSimple($unordered);
        return;
    }
    throw new Exception('Failed to identify charset='. $this->charset);
  }

  #===================================================================

  /**
   * Returns 'First'
   * @var string
   */
  public function getFirst() {
    return $this->first;
  }

  #===================================================================

  /**
   * Returns 'Last'
   * @var string
   */
  public function getLast() {
    return $this->last;
  }

  #===================================================================

  /**
   * Returns 'Middle'
   * @var string
   */
  public function getMiddle() {
    return $this->mb_ucname($this->middle);
  }

  #===================================================================

  /**
   * Returns 'First Last'
   * @var string
   */
  public function getFirstLast() {
    if ($this->charset == 'CJK') {
      return trim($this->first . $this->last);
    }
    return $this->mb_ucname(trim($this->first.' '.$this->last));
  }

  #===================================================================

  /**
   * Returns 'Last First'
   * @var string
   */
  public function getLastFirst() {
    if ($this->charset == 'CJK') {
      return trim($this->last . $this->first);
    }
    return $this->mb_ucname(trim($this->last.' '.$this->first));
  }

  #===================================================================

  /**
   * Returns 'First Middle Last'
   * @var string
   */
  public function getFirstMiddleLast() {
    if ($this->charset == 'CJK') {
      return trim($this->first . $this->last); # NOTE: CJK names don't have middle name.
    }
    return $this->mb_ucname(trim($this->first.' '.$this->middle.' '.$this->last));
  }

  #===================================================================

  /**
   * Simple (inaccurate) way to segment a name:
   * Detects which name is surname (last), given name (first), middle
   * according to order.
   * @var string
   */
  private function segmentSimple($unordered) {
    if (strpos($unordered, ' ') === false) {
      # Let's assume that it's last name
      $this->last = $unordered;
      return;
    }
    $arr = explode(' ', $unordered);
    $n = count($arr);
    $this->first = $arr[0];
    $this->last  = $arr[$n - 1];
    if ($n >= 3) {
      for ($x = 1; $x < $n - 1; $x++) {
        $this->middle .= $arr[$x];
      }
    }
  }

  #===================================================================

  /**
   * Cyrillic name:
   * Detects which name is surname (last), given name (first), middle
   * according to order.
   * @var string
   */
  private function segmentCyrillic($unordered) { # Владимир Владимирович Путин
    if (strpos($unordered, ' ') === false) {
      # Let's assume that it's last name
      $this->last = $unordered;
      return;
    }
    $arr = explode(' ', $unordered);
    $n = count($arr);
    $this->first = $arr[0];      # Владимир
    $this->last  = $arr[$n - 1]; # Путин
    if ($n >= 3) {
      for ($x = 1; $x < $n - 1; $x++) {
        $this->middle .= $arr[$x];
      }
    }
    #----
    if (preg_match('/\S+(ская|ова|ёва|ина)$/i', $this->first)) { # Russian female last name
      $temp  = $this->last;
      $this->last  = $this->first;
      $this->first = $temp;
    }
  }

  #===================================================================

  /**
   * CJK (Chinese/Japanese/Korean) name:
   * Detects which name is surname (last), given name (first)
   * according to length.
   * @var string
   */
  private function segmentCJK($unordered) {
    if (mb_strlen($unordered) == 1) {
      $this->last = $unordered;
      return;
    }
    elseif (mb_strlen($unordered) == 2 || mb_strlen($unordered) == 3) {
      $this->last  = mb_substr($unordered, 0, 1);
      $this->first = mb_substr($unordered, 1);
      return;
    }
    $this->last  = mb_substr($unordered, 0, 2);
    $this->first = mb_substr($unordered, 2);
  }

  #===================================================================

  /**
   * Latin name:
   * Detects which name is surname (last) according to all upper-case
   * (WONG Janet) and which is given name (first)
   * and accordning a dictionary of given names.
   * @var string
   */
  private function segmentLatin($unordered) {
    if (strpos($unordered, ' ') === false) {
      # Let's assume that it's last name
      $this->last = $unordered;
      return;
    }
    $arr = explode(' ', $unordered);
    foreach ($arr as $key => $val) {
      # Look for all upper case (WONG Janet)
      if (mb_strlen($val) > 1 && mb_convert_case($val, MB_CASE_UPPER, "UTF-8") == $val) {
        $this->last .= ' '.$val;
      }
      else {
        $this->first .= ' '.$val;
      }
    }
    $unordered = false;
    $this->first = trim($this->first);
    $this->last  = trim($this->last);
    #----
    if (empty($this->last)) {
      $unordered = $this->first;
      $this->first = '';
      $this->last  = '';
    }
    elseif (empty($this->first)) {
      $unordered = $this->last;
      $this->first = '';
      $this->last  = '';
    }
    else {
      # We found (WONG Janet)
      return;
    }
    #----
    if (!empty($unordered)) {
      $arr = explode(' ', $unordered);
      require __DIR__.'/dictionary-first-names.php';
      foreach ($arr as $key => $val) {
        # Last name usually isn't one character
        if (mb_strlen($val) > 1 && !in_array(mb_convert_case($val, MB_CASE_LOWER, "UTF-8"), $dict)) {
          $this->last .= ' '.$val;
        }
        else {
          $this->first .= ' '.$val;
        }
      }
      $this->first = trim($this->first);
      $this->last  = trim($this->last);
    }
    #----
    if (empty($this->last)) {
      $unordered = $this->first;
      $this->first = '';
      $this->last  = '';
      $arr = explode(' ', $unordered);
      foreach ($arr as $val) {
        if (empty($this->first)) {
          $this->first = $val;
        }
        else {
          $this->last .= ' '.$val;
        }
      }
      $this->last = trim($this->last);
    }
    elseif (empty($this->first)) {
      $unordered = $this->last;
      $this->first = '';
      $this->last  = '';
      $arr = explode(' ', $unordered);
      foreach ($arr as $val) {
        if (empty($this->first)) {
          $this->first = $val;
        }
        else {
          $this->last .= ' '.$val;
        }
      }
      $this->last = trim($this->last);
    }
    #----
    if (preg_match('/\S+á$/i', $this->first)) { # Czech female last name
      $temp  = $this->last;
      $this->last  = $this->first;
      $this->first = $temp;
    }
  }

  #===================================================================

  /**
   * UC First (name)
   * Handles hyphenated (Jean-Luc Picard) and apostrophised (Miles O'Brien) names.
   * @var string
   */
  private function mb_ucname($str) {
    $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    foreach (array("-", "'") as $delimiter) {
      if (strpos($str, $delimiter) !== false) {
        $str = implode($delimiter, array_map('mb_ucfirst', explode($delimiter, $str)));
      }
    }
    return $str;
  }

  #===================================================================
}