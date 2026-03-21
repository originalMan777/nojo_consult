<?php

namespace App\Http\Middleware;

use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'popupLeadSuccess' => fn () => $request->session()->get('popupLeadSuccess'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'popupManager' => fn () => $this->resolvePopupManager($request),
        ];
    }

    /**
     * @return array{pageKey:?string,leadCaptured:bool,isAuthenticated:bool,popups:array<int,array<string,mixed>>}
     */
    private function resolvePopupManager(Request $request): array
    {
        $pageKey = $this->resolvePopupPageKey($request);
        $leadCaptured = $request->cookie('nojo_lead_captured') === '1';
        $isAuthenticated = (bool) $request->user();

        if (! $pageKey || ! $this->popupTableIsReady()) {
            return [
                'pageKey' => $pageKey,
                'leadCaptured' => $leadCaptured,
                'isAuthenticated' => $isAuthenticated,
                'popups' => [],
            ];
        }

        $popups = Popup::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->latest('updated_at')
            ->get()
            ->filter(function (Popup $popup) use ($pageKey, $leadCaptured, $isAuthenticated, $request): bool {
                $targets = $popup->target_pages ?? [];
                if (! empty($targets) && ! in_array($pageKey, $targets, true)) {
                    return false;
                }

                if (! $this->passesAudienceRule($popup, $isAuthenticated)) {
                    return false;
                }

                if ($popup->suppress_if_lead_captured && $leadCaptured) {
                    return false;
                }

                if ($popup->suppression_scope === 'this_popup_only' && $this->popupSpecificCookieKey($popup, $request) !== null) {
                    return false;
                }

                return true;
            })
            ->values()
            ->map(fn (Popup $popup) => [
                'id' => $popup->id,
                'name' => $popup->name,
                'slug' => $popup->slug,
                'type' => $popup->type,
                'role' => $popup->role,
                'priority' => $popup->priority,
                'eyebrow' => $popup->eyebrow,
                'headline' => $popup->headline,
                'body' => $popup->body,
                'cta_text' => $popup->cta_text,
                'success_message' => $popup->success_message,
                'layout' => $popup->layout,
                'trigger_type' => $popup->trigger_type,
                'trigger_delay' => $popup->trigger_delay,
                'trigger_scroll' => $popup->trigger_scroll,
                'target_pages' => $popup->target_pages ?? [],
                'device' => $popup->device,
                'frequency' => $popup->frequency,
                'audience' => $popup->audience,
                'suppress_if_lead_captured' => $popup->suppress_if_lead_captured,
                'suppression_scope' => $popup->suppression_scope,
                'form_fields' => $popup->form_fields ?? ['name', 'email'],
                'lead_type' => $popup->lead_type,
                'post_submit_action' => $popup->post_submit_action,
                'post_submit_redirect_url' => $popup->post_submit_redirect_url,
            ])
            ->all();

        return [
            'pageKey' => $pageKey,
            'leadCaptured' => $leadCaptured,
            'isAuthenticated' => $isAuthenticated,
            'popups' => $popups,
        ];
    }

    private function passesAudienceRule(Popup $popup, bool $isAuthenticated): bool
    {
        return match ($popup->audience) {
            'authenticated' => $isAuthenticated,
            'guests' => ! $isAuthenticated,
            default => true,
        };
    }

    private function popupTableIsReady(): bool
    {
        if (! Schema::hasTable('popups')) {
            return false;
        }

        foreach (['role', 'priority', 'audience', 'suppress_if_lead_captured', 'suppression_scope', 'post_submit_action'] as $column) {
            if (! Schema::hasColumn('popups', $column)) {
                return false;
            }
        }

        return true;
    }

    private function popupSpecificCookieKey(Popup $popup, Request $request): ?string
    {
        $cookieKey = 'nojo_popup_submitted_'.Str::slug((string) $popup->slug, '_');

        return $request->cookie($cookieKey) === '1' ? $cookieKey : null;
    }

    private function resolvePopupPageKey(Request $request): ?string
    {
        $routeName = $request->route()?->getName();

        if ($routeName) {
            if (str_starts_with($routeName, 'admin.') || in_array($routeName, ['dashboard', 'login', 'register'], true)) {
                return null;
            }

            return match (true) {
                $routeName === 'home' => 'home',
                $routeName === 'about' => 'about',
                $routeName === 'services' => 'services',
                $routeName === 'buyers' => 'buyers',
                $routeName === 'sellers' => 'sellers',
                $routeName === 'consultation' => 'consultation',
                $routeName === 'resources' => 'resources',
                $routeName === 'contact' => 'contact',
                str_starts_with($routeName, 'blog.') => 'blog',
                default => null,
            };
        }

        return match ($request->path()) {
            '/', '' => 'home',
            'about' => 'about',
            'services' => 'services',
            'buyers' => 'buyers',
            'sellers' => 'sellers',
            'consultation' => 'consultation',
            'resources' => 'resources',
            'contact' => 'contact',
            default => str_starts_with($request->path(), 'blog') ? 'blog' : null,
        };
    }
}
