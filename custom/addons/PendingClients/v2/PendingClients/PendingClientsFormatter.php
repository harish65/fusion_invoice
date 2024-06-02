<?php

namespace FI\Modules\PendingClients;

class PendingClientsFormatter
{
    public static function phone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) == 10)
        {
            $phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }

        return $phone;
    }
}

