<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;

class CaptchaController extends Controller
{
    public function getCaptcha()
    {
        return CaptchaService::getCaptcha();
    }
}
