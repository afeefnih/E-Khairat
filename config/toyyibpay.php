<?php

return [

  'client_secret' => env('TOYYIBPAY_USER_SECRET_KEY', ''),
  'redirect_uri' => env('TOYYIBPAY_REDIRECT_URI', ''),
  'sandbox' => env('TOYYIBPAY_SANDBOX', true),
  'category_codes' => [
        'yuran_khairat' => env('TOYYIBPAY_CATEGORY_CODE_YURAN_KHAIRAT'),
        'infaq_khairat' => env('TOYYIBPAY_CATEGORY_CODE_INFAQ_KHAIRAT'),
    ],


];
