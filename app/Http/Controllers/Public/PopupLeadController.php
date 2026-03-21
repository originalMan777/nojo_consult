<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Models\PopupLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PopupLeadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $baseValidated = $request->validate([
            'popup_id' => ['required', 'integer', Rule::exists('popups', 'id')],
            'page_key' => ['nullable', 'string', 'max:100'],
            'source_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $popup = Popup::query()
            ->whereKey($baseValidated['popup_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $fields = collect($popup->form_fields ?? [])->values();

        if ($fields->isEmpty()) {
            $fields = collect(['email']);
        }

        $payload = $request->validate([
            'name' => $fields->contains('name')
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'],
            'email' => $fields->contains('email')
                ? ['required', 'email:rfc', 'max:255']
                : ['nullable', 'email:rfc', 'max:255'],
            'phone' => $fields->contains('phone')
                ? ['required', 'string', 'max:50']
                : ['nullable', 'string', 'max:50'],
            'message' => $fields->contains('message')
                ? ['required', 'string', 'max:5000']
                : ['nullable', 'string', 'max:5000'],
        ]);

        PopupLead::create([
            'popup_id' => $popup->id,
            'page_key' => $baseValidated['page_key'] ?? null,
            'source_url' => $baseValidated['source_url'] ?? $request->headers->get('referer'),
            'lead_type' => $popup->lead_type,
            'name' => $payload['name'] ?? null,
            'email' => $payload['email'] ?? null,
            'phone' => $payload['phone'] ?? null,
            'message' => $payload['message'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'popup_slug' => $popup->slug,
                'popup_type' => $popup->type,
                'popup_role' => $popup->role,
                'trigger_type' => $popup->trigger_type,
                'target_pages' => $popup->target_pages ?? [],
                'post_submit_action' => $popup->post_submit_action,
            ],
        ]);

        $cookieMinutes = 60 * 24 * 365;

        Cookie::queue(Cookie::make(
            'nojo_popup_submitted_'.Str::slug((string) $popup->slug, '_'),
            '1',
            $cookieMinutes,
            '/',
            null,
            false,
            false,
            false,
            'lax'
        ));

        if ($popup->suppression_scope === 'all_lead_popups') {
            Cookie::queue(Cookie::make(
                'nojo_lead_captured',
                '1',
                $cookieMinutes,
                '/',
                null,
                false,
                false,
                false,
                'lax'
            ));
        }

        return back()->with('popupLeadSuccess', $popup->success_message ?: 'Thanks. We received your information.');
    }
}
