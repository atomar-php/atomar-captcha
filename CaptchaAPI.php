<?php

namespace captcha;

use atomar\Atomar;
use crypto\Crypt;

/**
* This api provides methods for validating users via a captcha form
*/
class CaptchaAPI
{
  /**
   * Renders the catpcha inputs that will receive the user response and include the form token
   * @throws \RedBeanPHP\RedException
   */
  public static function twig_insert_form() {
    // house keeping
    $expired = db_date(time() - Atomar::get_variable('captcha_token_ttl', 3600)); // expire after one hour
    $sql_clean = <<<SQL
DELETE FROM `captcha`
WHERE `created_at` <= ?
SQL;
    \R::exec($sql_clean, array($expired));

    // prepare form
    $token = Crypt::randString(20);
    $num1 = Crypt::randInt(1, 20);
    $num2 = Crypt::randInt(1, 10);

    $captcha = \R::dispense('captcha');
    $captcha->token = $token;
    $captcha->answer = $num1 + $num2;
    $captcha->created_at = db_date();
    store($captcha);

    $html = <<<HTML
<fielset class="captcha">
  <legend>Validation</legend>
  <label for="captcha">Please complete the equation to prove you are a human:</label>
  $num1 + $num2 =
  <input type="text" id="captcha" name="captcha_answer" required/>
  <input type="hidden" name="captcha_token" value="$token"/>
</fielset>
HTML;
    echo $html;
  }

  /**
   * Checks if the captcha response is valid
   * @param $token the captcha form token
   * @param $answer the user response
   * @return bool
   */
  public static function is_valid($token, $answer) {
    // validate
    $captcha = \R::findOne('captcha', ' WHERE token=? ', array($token));
    if($captcha && $captcha->answer == $answer) {
      \R::trash($captcha);
      return true;
    } else {
      return false;
    }
  }
}