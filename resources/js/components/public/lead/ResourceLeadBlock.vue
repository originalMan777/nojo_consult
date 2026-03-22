<script setup lang="ts">
import { computed, ref } from 'vue';
import { resolveLeadIcon } from '@/lib/leadIcons';
import type { LeadBlockRenderModel } from '@/types/leadBlocks';
import { useLeadCaptureForm } from '@/composables/useLeadCaptureForm';

const props = defineProps<{
    model: LeadBlockRenderModel;
    previewMode?: boolean;
}>();

const localSuccess = ref(false);
const { form, isFormOpen, isSuccess, canSubmit, open, close, submit } = useLeadCaptureForm(props.model);

const iconComponent = computed(() => resolveLeadIcon(props.model.iconKey));

const success = computed(() => localSuccess.value || isSuccess.value);

const start = () => {
    if (props.previewMode) return;
    open();
};

const doSubmit = () => {
    if (props.previewMode) return;
    submit();
};

const closeForm = () => {
    if (props.previewMode) return;
    close();
};
</script>

<template>
    <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm ring-1 ring-black/5 md:p-8">
        <div class="grid gap-8 md:grid-cols-[360px_1fr] md:items-center">
            <div class="relative overflow-hidden rounded-3xl bg-gray-50 p-8 ring-1 ring-black/5">
                <div class="pointer-events-none absolute inset-0 opacity-60">
                    <div class="absolute -left-10 top-10 h-32 w-32 rounded-full bg-gray-200/60 blur-2xl"></div>
                    <div class="absolute -right-12 bottom-6 h-40 w-40 rounded-full bg-gray-300/50 blur-2xl"></div>
                </div>

                <p class="relative z-10 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                    Resource
                </p>

                <h3 class="relative z-10 mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                    {{ model.title }}
                </h3>

                <div class="relative z-10 mt-6 flex items-center gap-4">
                    <div
                        class="grid h-16 w-16 place-items-center rounded-2xl bg-white shadow-sm ring-1 ring-black/5"
                        :class="{'animate-[float_2.4s_ease-in-out_infinite]': !previewMode}"
                    >
                        <component v-if="iconComponent" :is="iconComponent" class="h-7 w-7 text-gray-900" />
                        <div v-else class="h-7 w-7 rounded bg-gray-200"></div>
                    </div>

                    <div class="text-sm text-gray-600">
                        <p class="font-medium text-gray-900">Quick download</p>
                        <p class="mt-1">Takes less than a minute.</p>
                    </div>
                </div>
            </div>

            <div>
                <p v-if="model.shortText" class="text-base leading-relaxed text-gray-700">
                    {{ model.shortText }}
                </p>

                <div class="mt-6">
                    <div v-if="success" class="rounded-2xl bg-gray-50 p-6 ring-1 ring-black/5">
                        <p class="text-sm font-semibold text-gray-900">You're all set.</p>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">
                            Thanks — we'll follow up shortly with your resource.
                        </p>
                    </div>

                    <div v-else>
                        <button
                            v-if="!isFormOpen"
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800"
                            @click="start"
                        >
                            {{ model.buttonText || 'Get the resource' }}
                        </button>

                        <div v-else class="mt-4 rounded-2xl bg-gray-50 p-6 ring-1 ring-black/5">
                            <div class="flex items-start justify-between gap-4">
                                <p class="text-sm font-semibold text-gray-900">
                                    Where should we send it?
                                </p>

                                <button
                                    type="button"
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 hover:text-gray-700"
                                    @click="closeForm"
                                >
                                    Close
                                </button>
                            </div>

                            <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="doSubmit">
                                <div class="md:col-span-1">
                                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                        First name
                                    </label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none ring-0 focus:border-gray-400"
                                        :disabled="previewMode"
                                    />
                                    <p v-if="form.errors.first_name" class="mt-2 text-xs text-red-600">
                                        {{ form.errors.first_name }}
                                    </p>
                                </div>

                                <div class="md:col-span-1">
                                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                        Email
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none ring-0 focus:border-gray-400"
                                        :disabled="previewMode"
                                    />
                                    <p v-if="form.errors.email" class="mt-2 text-xs text-red-600">
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div class="md:col-span-2 flex flex-wrap items-center gap-3">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="previewMode || !canSubmit"
                                    >
                                        {{ form.processing ? 'Submitting…' : 'Send it to me' }}
                                    </button>

                                    <p class="text-sm text-gray-600">
                                        No spam. Unsubscribe anytime.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes float {
    0% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0); }
}
</style>
