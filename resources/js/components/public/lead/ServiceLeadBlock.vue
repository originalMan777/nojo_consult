<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { resolveLeadIcon } from '@/lib/leadIcons';
import type { LeadBlockRenderModel } from '@/types/leadBlocks';

type ValuePoint = { icon_key: string; line: string };

const props = defineProps<{
    model: LeadBlockRenderModel;
    previewMode?: boolean;
}>();

const valuePoints = computed<ValuePoint[]>(() => {
    const vp = (props.model.content?.value_points ?? []) as ValuePoint[];
    return Array.isArray(vp) ? vp.slice(0, 3) : [];
});

const ctaLine = computed(() => (props.model.content?.cta_line as string) || 'Get started in minutes.');
const reassurance = computed(() => (props.model.content?.reassurance_text as string) || '');

const iconFor = (key: string) => resolveLeadIcon(key);

const consultHref = computed(() => {
    if (props.previewMode) return '#';
    return route('consultation', {
        source: 'service_lead_block',
        lead_box_id: props.model.leadBoxId,
        lead_slot_key: props.model.context?.slotKey,
        page_key: props.model.context?.pageKey,
        });
});
</script>

<template>
    <section class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-4 shadow-sm ring-1 ring-black/5 md:p-5">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-56 w-56 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gray-200/35 blur-3xl"></div>
        </div>

        <div class="relative z-10 grid gap-4 md:grid-cols-[7fr_6fr_7fr] md:items-center">
            <!-- LEFT (35%) -->
            <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-black/5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">
                    Service
                </p>

                <div class="mt-4 space-y-3">
                    <div v-for="(vp, idx) in valuePoints" :key="idx" class="flex items-start gap-3">
                        <div class="mt-0.5 grid h-8 w-8 place-items-center rounded-xl bg-white ring-1 ring-black/5">
                            <component v-if="iconFor(vp.icon_key)" :is="iconFor(vp.icon_key)" class="h-4 w-4 text-gray-800" />
                            <div v-else class="h-4 w-4 rounded bg-gray-200"></div>
                        </div>
                        <p class="text-sm font-medium leading-snug text-gray-900">
                            {{ vp.line }}
                        </p>
                    </div>

                    <div v-if="!valuePoints.length" class="text-sm text-gray-600">
                        Add 3 value points in the Service Lead Box editor.
                    </div>
                </div>
            </div>

            <!-- CENTER (30%) -->
            <div class="relative overflow-hidden rounded-2xl bg-gray-900 p-4 text-white shadow-sm ring-1 ring-black/10">
                <div class="pointer-events-none absolute inset-0 opacity-70">
                    <div class="absolute -left-16 top-4 h-36 w-36 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute -right-14 bottom-4 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                </div>

                <div class="relative z-10">
                    <p class="text-sm font-semibold leading-snug">
                        {{ ctaLine }}
                    </p>

                    <div class="mt-3">
                        <Link
                            v-if="!previewMode"
                            :href="consultHref"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-white/90"
                        >
                            {{ model.buttonText || 'Request a call' }}
                        </Link>

                        <button
                            v-else
                            type="button"
                            class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-white/80 px-4 py-2.5 text-sm font-semibold text-gray-900 opacity-70"
                            disabled
                        >
                            {{ model.buttonText || 'Request a call' }}
                        </button>

                        <p
                            v-if="reassurance"
                            class="mt-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/70"
                        >
                            {{ reassurance }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT (35%) -->
            <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-black/5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">
                    What happens next
                </p>

                <h3 class="mt-2 text-lg font-semibold tracking-tight text-gray-900">
                    {{ model.title }}
                </h3>

                <p v-if="model.shortText" class="mt-2 text-sm leading-relaxed text-gray-600">
                    {{ model.shortText }}
                </p>

                <p v-else class="mt-2 text-sm leading-relaxed text-gray-600">
                    Add explanation text in the Service Lead Box editor.
                </p>
            </div>
        </div>
    </section>
</template>
