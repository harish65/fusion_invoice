<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Composers;

class SkinComposer
{
    public function compose($view)
    {
        $colourCombination = explode('!!!', config('fi.topBarColor'));
        $skin              = (config('fi.skin') ?: 'light-mode');
        $view->with('skin', $skin);
        $view->with('skinClass', $skin);
        $view->with('navClass', str_replace('-mode', '', $skin));
        $view->with('topBarColor', (isset($colourCombination[0]) ? $colourCombination[0] : 'brand-link'));
        $view->with('topBarColorText', (isset($colourCombination[1]) ? $colourCombination[1] : 'info'));
        $view->with('topBarLogoutColorText', (isset($colourCombination[2]) ? $colourCombination[2] : 'danger'));
        $view->with('topBarSymbolColorText', (isset($colourCombination[3]) ? $colourCombination[3] : 'light'));
        $view->with('darkModeForInvoiceAndQuoteTemplate', 'table.table-dark tr:nth-child(odd) td {background: #2c3034;}  table.table-dark {color: #fff !important;background-color: #212529 !important;}  body.body-dark {color: #fff !important;background: #454d55 !important;}  body.body-dark a {color: #4ba1df;border-bottom: 1px solid currentColor;text-decoration: none;}  table.table-dark td, .table-dark th, .table-dark thead th {border-color: #32383e;color: white;}  table.body-dark .section-header {border-color: #32383e;color: white;}  table.body-dark .info {color: #b2b2b2;font-weight: bold;}  table.body-dark h1 {color: #b2b2b2;font-weight: bold;}');
    }
}