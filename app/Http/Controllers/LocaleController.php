<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Change la langue de l'application.
     */
    public function setLanguage(Request $request)
    {
        $locale = $request->input('locale', 'fr');

        if (in_array($locale, ['fr', 'en'])) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }
}
