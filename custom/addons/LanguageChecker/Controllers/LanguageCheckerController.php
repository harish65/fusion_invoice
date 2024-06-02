<?php

namespace Addons\LanguageChecker\Controllers;

use Addons\LanguageChecker\Libraries\LanguageChecker;
use FI\Http\Controllers\Controller;

class LanguageCheckerController extends Controller
{
    private $languageChecker;

    public function __construct(LanguageChecker $languageChecker)
    {
        $this->languageChecker = $languageChecker;
    }

    public function index()
    {
        return view('languagechecker.index')
            ->with('validStrings', $this->languageChecker->getValidStrings(request('language', 'en')))
            ->with('invalidStrings', $this->languageChecker->getInvalidStrings());
    }
}