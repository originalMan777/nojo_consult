import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed } from 'vue';

export function usePublicAuthNavigation() {
    const page = usePage<any>();

    const user = computed(() => page.props.auth?.user ?? null);
    const displayName = computed(
        () => user.value?.name ?? user.value?.email ?? 'Account',
    );
    const isAdmin = computed(() => Boolean(user.value?.is_admin));
    const hasAdminWorkspace = computed(() => isAdmin.value);
    const profileHref = computed(() => route('profile.edit'));
    const dashboardHref = computed(() => route('dashboard'));

    return {
        user,
        displayName,
        isAdmin,
        hasAdminWorkspace,
        profileHref,
        dashboardHref,
    };
}
