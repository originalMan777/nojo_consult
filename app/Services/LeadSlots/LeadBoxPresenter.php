<?php

namespace App\Services\LeadSlots;

use App\Models\LeadAssignment;
use App\Models\LeadBox;

class LeadBoxPresenter
{
    /**
     * @return array{
     *   leadBoxId:int,
     *   type:string,
     *   title:string,
     *   shortText:?string,
     *   buttonText:?string,
     *   iconKey:?string,
     *   content:array<string,mixed>,
     *   context:array{slotKey:string,pageKey:string}
     * }
     */
    public function present(LeadBox $leadBox, LeadAssignment $assignment, string $slotKey, string $pageKey): array
    {
        return [
            'leadBoxId' => $leadBox->id,
            'type' => $leadBox->type,
            'title' => $assignment->override_title ?? $leadBox->title,
            'shortText' => $assignment->override_short_text ?? $leadBox->short_text,
            'buttonText' => $assignment->override_button_text ?? $leadBox->button_text,
            'iconKey' => $leadBox->icon_key,
            'content' => $leadBox->content ?? [],
            'context' => [
                'slotKey' => $slotKey,
                'pageKey' => $pageKey,
            ],
        ];
    }
}
