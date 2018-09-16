<?php

namespace App\Services;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Session;

class CaptchaService
{
    public static function getCaptcha()
    {
        $phrase  = new PhraseBuilder();
        $length  = $phrase->build(4);
        $builder = new CaptchaBuilder($length);
        $builder->build($width = 100, $height = 35, $font = null);
        session(array('captcha' => $builder->getPhrase()));

        return $builder->inline();
    }
}
