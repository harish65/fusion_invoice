<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Notes\Events;

use FI\Events\Event;
use FI\Modules\Notes\Models\Note;
use Illuminate\Queue\SerializesModels;

class AddTransitionTags extends Event
{
    use SerializesModels;

    public $note;
    public $actionType;
    public $previousValue;
    public $currentValue;
    public $detail;
    public $userId;
    public $tagName;
    public $tagId;
    public $isDirtyNote;

    public function __construct(Note $note, $actionType, $previousValue = null, $currentValue = null, $userId = null, $tagName = null, $tagId = null, $isDirtyNote = false)
    {
        $this->note          = $note;
        $this->actionType    = $actionType;
        $this->previousValue = $previousValue;
        $this->currentValue  = $currentValue;
        $this->userId        = $userId;
        $this->tagId         = $tagId;
        $this->isDirtyNote   = $isDirtyNote;

        if ($actionType == 'note_tag_deleted' || $actionType == 'note_tag_updated')
        {
            $this->detail = [
                'short_text' => $note->note,
                'number'     => $note->id,
                'name'       => $note->name,
                'tag_name'   => '<i><b>&ldquo;' . $tagName . '&rdquo;</b></i>',
            ];
        }
    }
}