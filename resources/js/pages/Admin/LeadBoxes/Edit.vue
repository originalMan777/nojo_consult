<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';
import LeadBlockRenderer from '@/components/public/lead/LeadBlockRenderer.vue';
import type { LeadBlockRenderModel } from '@/types/leadBlocks';

type LeadBoxType = 'resource' | 'service' | 'offer';
type ValuePoint = { icon_key: string; line: string };

type LeadBoxPayload = {
    id: number;
    type: LeadBoxType;
    status: 'draft' | 'active' | 'inactive';
    internal_name: string;
    title: string;
    short_text: string | null;
    button_text: string | null;
    icon_key: string | null;
    content: Record<string, unknown> | null;
};

const props = defineProps<{
    leadBox: LeadBoxPayload;
    statuses: string[];
    icons: string[];
    visualPresets: string[];
}>();

const activeType = ref<LeadBoxType>(props.leadBox.type);

const content = computed(() => props.leadBox.content ?? {});

const resourceForm = useForm({
    status: props.leadBox.type === 'resource' ? props.leadBox.status : 'draft',
    internal_name: props.leadBox.type === 'resource' ? props.leadBox.internal_name : '',
    title: props.leadBox.type === 'resource' ? props.leadBox.title : '',
    short_text: props.leadBox.type === 'resource' ? props.leadBox.short_text ?? '' : '',
    button_text: props.leadBox.type === 'resource' ? props.leadBox.button_text ?? 'Get the resource' : 'Get the resource',
    icon_key: props.leadBox.type === 'resource' ? props.leadBox.icon_key ?? 'book-open' : 'book-open',
    visual_preset:
        props.leadBox.type === 'resource'
            ? ((content.value['visual_preset'] as string) || 'default')
            : 'default',
});

const defaultValuePoints: ValuePoint[] = [
    { icon_key: 'shield-check', line: 'Clear guidance' },
    { icon_key: 'clock', line: 'Fast response' },
    { icon_key: 'message-square', line: 'Practical next steps' },
];

const serviceForm = useForm({
    status: props.leadBox.type === 'service' ? props.leadBox.status : 'draft',
    internal_name: props.leadBox.type === 'service' ? props.leadBox.internal_name : '',
    title: props.leadBox.type === 'service' ? props.leadBox.title : '',
    short_text: props.leadBox.type === 'service' ? props.leadBox.short_text ?? '' : '',
    button_text: props.leadBox.type === 'service' ? props.leadBox.button_text ?? 'Request a call' : 'Request a call',
    cta_line: props.leadBox.type === 'service' ? ((content.value['cta_line'] as string) || '') : '',
    reassurance_text:
        props.leadBox.type === 'service' ? ((content.value['reassurance_text'] as string) || '') : '',
    value_points:
        props.leadBox.type === 'service'
            ? (((content.value['value_points'] as ValuePoint[]) || defaultValuePoints).slice(0, 3) as ValuePoint[])
            : (defaultValuePoints as ValuePoint[]),
});


const defaultOfferValuePoints: ValuePoint[] = [
    { icon_key: 'sparkles', line: 'Personalized strategy' },
    { icon_key: 'check-circle-2', line: 'Clear next steps' },
    { icon_key: 'clock', line: 'Fast turnaround' },
];

const offerForm = useForm({
    status: props.leadBox.type === 'offer' ? props.leadBox.status : 'draft',
    internal_name: props.leadBox.type === 'offer' ? props.leadBox.internal_name : '',
    title: props.leadBox.type === 'offer' ? props.leadBox.title : '',
    breakdown_line_1: props.leadBox.type === 'offer' ? props.leadBox.short_text ?? '' : '',
    breakdown_line_2: props.leadBox.type === 'offer' ? ((content.value['breakdown_line_2'] as string) || '') : '',
    button_text: props.leadBox.type === 'offer' ? props.leadBox.button_text ?? 'Claim your spot' : 'Claim your spot',
    cta_line: props.leadBox.type === 'offer' ? ((content.value['cta_line'] as string) || '') : '',
    reassurance_text: props.leadBox.type === 'offer' ? ((content.value['reassurance_text'] as string) || '') : '',
    value_points:
        props.leadBox.type === 'offer'
            ? (((content.value['value_points'] as ValuePoint[]) || defaultOfferValuePoints).slice(0, 3) as ValuePoint[])
            : (defaultOfferValuePoints as ValuePoint[]),
});

const submit = () => {
    if (activeType.value === 'resource') {
        resourceForm.put(route('admin.lead-boxes.resource.update', props.leadBox.id), { preserveScroll: true });
        return;
    }
    if (activeType.value === 'service') {
        serviceForm.put(route('admin.lead-boxes.service.update', props.leadBox.id), { preserveScroll: true });
        return;
    }
    if (activeType.value === 'offer') {
        offerForm.put(route('admin.lead-boxes.offer.update', props.leadBox.id), { preserveScroll: true });
    }
};

const previewModel = computed<LeadBlockRenderModel>(() => {
    if (activeType.value === 'offer') {
        return {
            leadBoxId: props.leadBox.id,
            type: 'offer',
            title: offerForm.title,
            shortText: offerForm.breakdown_line_1 || null,
            buttonText: offerForm.button_text || null,
            iconKey: null,
            content: {
                breakdown_line_2: offerForm.breakdown_line_2,
                cta_line: offerForm.cta_line,
                reassurance_text: offerForm.reassurance_text || null,
                value_points: offerForm.value_points,
            },
            context: { slotKey: 'home_bottom', pageKey: 'home' },
        };
    }

    if (activeType.value === 'service') {
        return {
            leadBoxId: props.leadBox.id,
            type: 'service',
            title: serviceForm.title,
            shortText: serviceForm.short_text || null,
            buttonText: serviceForm.button_text || null,
            iconKey: null,
            content: {
                cta_line: serviceForm.cta_line,
                reassurance_text: serviceForm.reassurance_text || null,
                value_points: serviceForm.value_points,
            },
            context: { slotKey: 'home_mid', pageKey: 'home' },
        };
    }

    return {
        leadBoxId: props.leadBox.id,
        type: 'resource',
        title: resourceForm.title,
        shortText: resourceForm.short_text || null,
        buttonText: resourceForm.button_text || null,
        iconKey: resourceForm.icon_key || null,
        content: { visual_preset: resourceForm.visual_preset },
        context: { slotKey: 'home_intro', pageKey: 'home' },
    };
});

const lockedTypeTooltip = computed(() => 'Type is locked after creation.');
</script>

<template>
    <Head title="Edit Lead Box" />

    <AdminLayout>
        <div class="h-full p-4">
            <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Lead Boxes
                            </p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">
                                Edit Lead Box
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
                                Type is locked after creation. Update the template fields below.
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Link
                                :href="route('admin.lead-boxes.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50"
                            >
                                Back to Lead Boxes
                            </Link>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="resourceForm.processing || serviceForm.processing || offerForm.processing"
                                @click="submit"
                            >
                                Save
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2" :title="lockedTypeTooltip">
                        <button
                            type="button"
                            class="rounded-xl px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                            :class="
                                activeType === 'resource'
                                    ? 'bg-gray-900 text-white ring-gray-900'
                                    : 'bg-gray-100 text-gray-500 ring-gray-200'
                            "
                            disabled
                        >
                            Resource
                        </button>

                        <button
                            type="button"
                            class="rounded-xl px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                            :class="
                                activeType === 'service'
                                    ? 'bg-gray-900 text-white ring-gray-900'
                                    : 'bg-gray-100 text-gray-500 ring-gray-200'
                            "
                            disabled
                        >
                            Service
                        </button>

                        <button
                            type="button"
                            class="rounded-xl px-4 py-2 text-sm font-semibold ring-1 ring-inset"
                            :class="
                                activeType === 'offer'
                                    ? 'bg-gray-900 text-white ring-gray-900'
                                    : 'bg-gray-100 text-gray-500 ring-gray-200'
                            "
                            disabled
                        >
                            Offer
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto p-6">
                    <div class="grid gap-8 lg:grid-cols-[420px_1fr]">
                        <!-- FORM -->
                        <form class="space-y-5" @submit.prevent="submit">
                            <!-- RESOURCE -->
                            <div v-if="activeType === 'resource'" class="space-y-5">
                                <div class="rounded-2xl border border-gray-200 p-5">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Status
                                            </label>
                                            <select
                                                v-model="resourceForm.status"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            >
                                                <option v-for="s in props.statuses" :key="s" :value="s">{{ s }}</option>
                                            </select>
                                            <p v-if="resourceForm.errors.status" class="mt-2 text-xs text-red-600">
                                                {{ resourceForm.errors.status }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Internal name
                                            </label>
                                            <input
                                                v-model="resourceForm.internal_name"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="resourceForm.errors.internal_name" class="mt-2 text-xs text-red-600">
                                                {{ resourceForm.errors.internal_name }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Title
                                            </label>
                                            <input
                                                v-model="resourceForm.title"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="resourceForm.errors.title" class="mt-2 text-xs text-red-600">
                                                {{ resourceForm.errors.title }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Short text
                                            </label>
                                            <textarea
                                                v-model="resourceForm.short_text"
                                                rows="4"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="resourceForm.errors.short_text" class="mt-2 text-xs text-red-600">
                                                {{ resourceForm.errors.short_text }}
                                            </p>
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                    Button text
                                                </label>
                                                <input
                                                    v-model="resourceForm.button_text"
                                                    type="text"
                                                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                />
                                                <p v-if="resourceForm.errors.button_text" class="mt-2 text-xs text-red-600">
                                                    {{ resourceForm.errors.button_text }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                    Icon key
                                                </label>
                                                <input
                                                    v-model="resourceForm.icon_key"
                                                    type="text"
                                                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                />
                                                <p v-if="resourceForm.errors.icon_key" class="mt-2 text-xs text-red-600">
                                                    {{ resourceForm.errors.icon_key }}
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Visual preset
                                            </label>
                                            <select
                                                v-model="resourceForm.visual_preset"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            >
                                                <option v-for="p in props.visualPresets" :key="p" :value="p">{{ p }}</option>
                                            </select>
                                            <p v-if="resourceForm.errors.visual_preset" class="mt-2 text-xs text-red-600">
                                                {{ resourceForm.errors.visual_preset }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SERVICE -->
                            <div v-else-if="activeType === 'service'" class="space-y-5">
                                <div class="rounded-2xl border border-gray-200 p-5">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Status
                                            </label>
                                            <select
                                                v-model="serviceForm.status"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            >
                                                <option v-for="s in props.statuses" :key="s" :value="s">{{ s }}</option>
                                            </select>
                                            <p v-if="serviceForm.errors.status" class="mt-2 text-xs text-red-600">
                                                {{ serviceForm.errors.status }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Internal name
                                            </label>
                                            <input
                                                v-model="serviceForm.internal_name"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="serviceForm.errors.internal_name" class="mt-2 text-xs text-red-600">
                                                {{ serviceForm.errors.internal_name }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Title (right zone headline)
                                            </label>
                                            <input
                                                v-model="serviceForm.title"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="serviceForm.errors.title" class="mt-2 text-xs text-red-600">
                                                {{ serviceForm.errors.title }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Explanation text (right zone)
                                            </label>
                                            <textarea
                                                v-model="serviceForm.short_text"
                                                rows="4"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="serviceForm.errors.short_text" class="mt-2 text-xs text-red-600">
                                                {{ serviceForm.errors.short_text }}
                                            </p>
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                    Button text
                                                </label>
                                                <input
                                                    v-model="serviceForm.button_text"
                                                    type="text"
                                                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                />
                                                <p v-if="serviceForm.errors.button_text" class="mt-2 text-xs text-red-600">
                                                    {{ serviceForm.errors.button_text }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                    CTA line (center)
                                                </label>
                                                <input
                                                    v-model="serviceForm.cta_line"
                                                    type="text"
                                                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                />
                                                <p v-if="serviceForm.errors.cta_line" class="mt-2 text-xs text-red-600">
                                                    {{ serviceForm.errors.cta_line }}
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Reassurance text (optional)
                                            </label>
                                            <input
                                                v-model="serviceForm.reassurance_text"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="serviceForm.errors.reassurance_text" class="mt-2 text-xs text-red-600">
                                                {{ serviceForm.errors.reassurance_text }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Value points (exactly 3)
                                            </p>

                                            <div class="mt-3 space-y-3">
                                                <div
                                                    v-for="(vp, idx) in serviceForm.value_points"
                                                    :key="idx"
                                                    class="grid gap-3 sm:grid-cols-[160px_1fr]"
                                                >
                                                    <select
                                                        v-model="vp.icon_key"
                                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                    >
                                                        <option v-for="i in props.icons" :key="i" :value="i">{{ i }}</option>
                                                    </select>

                                                    <input
                                                        v-model="vp.line"
                                                        type="text"
                                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                    />
                                                </div>
                                            </div>

                                            <p v-if="serviceForm.errors.value_points" class="mt-2 text-xs text-red-600">
                                                {{ serviceForm.errors.value_points }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="activeType === 'offer'" class="space-y-5">
                                <div class="rounded-2xl border border-gray-200 p-5">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Status
                                            </label>
                                            <select
                                                v-model="offerForm.status"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            >
                                                <option v-for="s in props.statuses" :key="s" :value="s">{{ s }}</option>
                                            </select>
                                            <p v-if="offerForm.errors.status" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.status }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Internal name
                                            </label>
                                            <input
                                                v-model="offerForm.internal_name"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.internal_name" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.internal_name }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Offer title
                                            </label>
                                            <input
                                                v-model="offerForm.title"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.title" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.title }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Breakdown line 1
                                            </label>
                                            <input
                                                v-model="offerForm.breakdown_line_1"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.breakdown_line_1" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.breakdown_line_1 }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Breakdown line 2
                                            </label>
                                            <input
                                                v-model="offerForm.breakdown_line_2"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.breakdown_line_2" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.breakdown_line_2 }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                CTA title / action line
                                            </label>
                                            <input
                                                v-model="offerForm.cta_line"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.cta_line" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.cta_line }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Button text
                                            </label>
                                            <input
                                                v-model="offerForm.button_text"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.button_text" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.button_text }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Reassurance text (optional)
                                            </label>
                                            <input
                                                v-model="offerForm.reassurance_text"
                                                type="text"
                                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                            />
                                            <p v-if="offerForm.errors.reassurance_text" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.reassurance_text }}
                                            </p>
                                        </div>

                                        <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-black/5">
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                                Value points (exactly 3)
                                            </p>

                                            <div class="mt-3 space-y-3">
                                                <div
                                                    v-for="(vp, idx) in offerForm.value_points"
                                                    :key="idx"
                                                    class="grid gap-3 sm:grid-cols-[160px_1fr]"
                                                >
                                                    <select
                                                        v-model="vp.icon_key"
                                                        class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                    >
                                                        <option v-for="i in props.icons" :key="i" :value="i">{{ i }}</option>
                                                    </select>

                                                    <input
                                                        v-model="vp.line"
                                                        type="text"
                                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900"
                                                    />
                                                </div>
                                            </div>

                                            <p v-if="offerForm.errors.value_points" class="mt-2 text-xs text-red-600">
                                                {{ offerForm.errors.value_points }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="rounded-2xl border border-gray-200 p-6 text-sm text-gray-600">
                                This Lead Box type is not supported yet.
                            </div>
                        </form>

                        <!-- PREVIEW -->
                        <div class="space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Preview
                            </p>
                            <LeadBlockRenderer :model="previewModel" preview-mode />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
