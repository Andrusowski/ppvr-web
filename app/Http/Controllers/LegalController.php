<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function getNotice()
    {
        return view('legal.notice');
    }

    public function getPrivacy()
    {
        return view('legal.privacy');
    }
}
