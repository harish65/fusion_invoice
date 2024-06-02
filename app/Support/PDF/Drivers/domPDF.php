<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\PDF\Drivers;

use Dompdf\Dompdf as PDF;
use Dompdf\Options;
use FI\Support\PDF\PDFAbstract;

class domPDF extends PDFAbstract
{
    private function getPdf($html)
    {
        $options = new Options();

        $options->setTempDir(storage_path('/'));
        $options->setFontDir(storage_path('/'));
        $options->setFontCache(storage_path('/'));
        $options->setLogOutputFile(storage_path('dompdf_log'));
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsFontSubsettingEnabled(true);

        $pdf = new PDF($options);

        $pdf->setPaper($this->paperSize, $this->paperOrientation);
        $pdf->loadHtml($html);

        $pdf->render();

        return $pdf;
    }

    public function getOutput($html)
    {
        $pdf = $this->getPdf($html);

        return $pdf->output();
    }

    public function save($html, $filename)
    {
        $createPdf = true;
        
        if (file_exists($filename)) 
        {                   
            try
            {
                unlink($filename);
            }
            catch (Exception $e)
            {
                $createPdf = false;
                Log::error('Attempt to remove existing PDf before recreation failed.' . $e->getMessage());
            }
        }

        if ($createPdf)
        {
            file_put_contents($filename, $this->getOutput($html));
        }
    }

    public function download($html, $filename)
    {
        $response = response($this->getOutput($html));

        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response->send();
    }
}