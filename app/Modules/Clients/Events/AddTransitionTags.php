<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients\Events;

use FI\Events\Event;
use FI\Modules\Clients\Models\Client;
use Illuminate\Queue\SerializesModels;

class AddTransitionTags extends Event
{
    use SerializesModels;

    public $client;
    public $actionType;
    public $previousValue;
    public $currentValue;
    public $detail;
    public $userId;
    public $tagName;
    public $tagId;

    public function __construct(Client $client, $actionType, $previousValue = null, $currentValue = null, $userId = null, $tagName = null, $tagId = null)
    {

        $this->client        = $client;
        $this->actionType    = $actionType;
        $this->previousValue = $previousValue;
        $this->currentValue  = $currentValue;
        $this->userId        = $userId;
        $this->tagId         = $tagId;

        if ($actionType == 'client_tag_deleted' || $actionType == 'client_tag_updated')
        {
            $this->detail = [
                'number'   => $client->id,
                'name'     => $client->name,
                'tag_name' => '<i><b>&ldquo;' . $tagName . '&rdquo;</b></i>',
            ];
        }
    }
}
