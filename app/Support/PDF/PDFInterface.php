<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\PDF;

interface PDFInterface
{
    public function save($html, $filename);

    public function download($html, $filename);

    public function setPaperSize($paperSize);

    public function setPaperOrientation($paperOrientation);
}