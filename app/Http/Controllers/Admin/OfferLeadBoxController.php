<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadBox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfferLeadBoxController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $leadBox = LeadBox::create([
            'type' => LeadBox::TYPE_OFFER,
            'status' => $validated['status'],
            'internal_name' => $validated['internal_name'],
            'title' => $validated['title'],
            'short_text' => $validated['breakdown_line_1'],
            'button_text' => $validated['button_text'] ?? null,
            'icon_key' => null,
            'content' => [
                'breakdown_line_2' => $validated['breakdown_line_2'],
                'cta_line' => $validated['cta_line'],
                'reassurance_text' => $validated['reassurance_text'] ?? null,
                'value_points' => $validated['value_points'],
            ],
            'settings' => [],
        ]);

        return redirect()
            ->route('admin.lead-boxes.edit', $leadBox)
            ->with('success', 'Offer Lead Box created.');
    }

    public function update(Request $request, LeadBox $leadBox): RedirectResponse
    {
        abort_unless($leadBox->type === LeadBox::TYPE_OFFER, 404);

        $validated = $this->validatePayload($request);

        $leadBox->update([
            'status' => $validated['status'],
            'internal_name' => $validated['internal_name'],
            'title' => $validated['title'],
            'short_text' => $validated['breakdown_line_1'],
            'button_text' => $validated['button_text'] ?? null,
            'content' => [
                'breakdown_line_2' => $validated['breakdown_line_2'],
                'cta_line' => $validated['cta_line'],
                'reassurance_text' => $validated['reassurance_text'] ?? null,
                'value_points' => $validated['value_points'],
            ],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Offer Lead Box updated.');
    }

    /**
     * @return array{
     *   status:string,
     *   internal_name:string,
     *   title:string,
     *   breakdown_line_1:string,
     *   breakdown_line_2:string,
     *   button_text:?string,
     *   cta_line:string,
     *   reassurance_text:?string,
     *   value_points:array<int,array{icon_key:string,line:string}>
     * }
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'status' => ['required', Rule::in(config('lead_blocks.statuses'))],
            'internal_name' => ['required', 'string', 'max:160'],
            'title' => ['required', 'string', 'max:200'],
            'breakdown_line_1' => ['required', 'string', 'max:220'],
            'breakdown_line_2' => ['required', 'string', 'max:220'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'cta_line' => ['required', 'string', 'max:220'],
            'reassurance_text' => ['nullable', 'string', 'max:220'],
            'value_points' => ['required', 'array', 'size:3'],
            'value_points.*.icon_key' => ['required', 'string', Rule::in(config('lead_blocks.icons'))],
            'value_points.*.line' => ['required', 'string', 'max:160'],
        ]);
    }
}
