<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Import\Models;

use Illuminate\Database\Eloquent\Model;

class ImportMapping extends Model
{
    protected $table = 'import_mappings';

    protected $guarded = [];

    protected $casts = [
        'description' => 'array',
        'is_default'  => 'boolean',
    ];


    public static function getMappingsByType($type)
    {
        return self::where('type', $type)->get();
    }

    public static function getDefaultMappingByType($type)
    {
        return $default = self::where([['type', '=', $type], ['is_default', '=', '1']])->first();
    }
}