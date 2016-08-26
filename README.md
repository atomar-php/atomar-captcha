Captcha
========

This extension provides a very minimalistic captcha form.

###Useage
Simply insert the captcha form into your template

>       <form ...>
>           ...
>           {{ captcha_form() }}
>       </form>

Then validate the response in your controller

>       function POST($matches = array()) {
>           if(CaptchaAPI::is_valid($_REQUEST['catpcha_token'], $_REQUEST['captcha_answer'])) {
>               // success
>           } else {
>               // failed
>           }
>       }

That's it! A new token with a valid answer is generated each time someone views the page. These could pile up so the extension will automatically remove expired captcha tokens upon page load.