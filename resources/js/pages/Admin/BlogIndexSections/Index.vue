<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';


type SectionKey = 'wide_section' | 'cluster_left' | 'cluster_right';

type CategoryOption = {
    id: number;
    name: string;
    slug: string;
};

type SectionDto = {
    id: number | null;
    section_key: SectionKey;
    enabled: boolean;
    source_type: 'latest' | 'featured' | 'category';
    category_id: number | null;
    title_override: string | null;
};

type SourceOption = {
    value: 'latest' | 'featured' | 'category';
    label: string;
};

const props = defineProps<{
    sections: Record<SectionKey, SectionDto>;
    categories: CategoryOption[];
    sourceOptions: SourceOption[];
}>();

const page = usePage<any>();
const flashSuccess = computed(() => page.props?.flash?.success ?? '');

const form = useForm({
    sections: {
        wide_section: {
            enabled: !!props.sections.wide_section.enabled,
            source_type: props.sections.wide_section.source_type,
            category_id: props.sections.wide_section.category_id,
            title_override: props.sections.wide_section.title_override ?? '',
        },
        cluster_left: {
            enabled: !!props.sections.cluster_left.enabled,
            source_type: props.sections.cluster_left.source_type,
            category_id: props.sections.cluster_left.category_id,
            title_override: props.sections.cluster_left.title_override ?? '',
        },
        cluster_right: {
            enabled: !!props.sections.cluster_right.enabled,
            source_type: props.sections.cluster_right.source_type,
            category_id: props.sections.cluster_right.category_id,
            title_override: props.sections.cluster_right.title_override ?? '',
        },
    },
});

const sectionMeta: Array<{ key: SectionKey; title: string; description: string }> = [
    {
        key: 'wide_section',
        title: 'Wide Section',
        description: 'Thin interrupt strip that appears after the first 6-post block.',
    },
    {
        key: 'cluster_left',
        title: 'Cluster Left',
        description: 'Left category cluster tile shown after the second 6-post block.',
    },
    {
        key: 'cluster_right',
        title: 'Cluster Right',
        description: 'Right category cluster tile shown after the second 6-post block.',
    },
];

const save = () => {
    form.put(route('admin.blog-index-sections.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Blog Index Sections" />

    <AdminLayout>
        <div class="p-6">
            <form @submit.prevent="save" class="space-y-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                            Blog Index Controls
                        </p>
                        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">
                            Blog Index Sections
                        </h1>
                        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
                            Control what the fixed blog interrupt sections show without changing the blog index layout.
                        </p>
                    </div>

                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving…' : 'Save Sections' }}
                    </PrimaryButton>
                </div>

                <div v-if="flashSuccess" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ flashSuccess }}
                </div>

                <div class="grid gap-6 xl:grid-cols-3">
                    <section
                        v-for="meta in sectionMeta"
                        :key="meta.key"
                        class="space-y-4 rounded-lg border bg-white p-4"
                    >
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ meta.title }}
                            </h2>
                            <p class="mt-1 text-sm leading-relaxed text-gray-600">
                                {{ meta.description }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label class="flex items-start gap-3">
                                <input
                                    v-model="form.sections[meta.key].enabled"
                                    type="checkbox"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                                />
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Enabled</div>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-600">
                                        Show this fixed section on the blog index when it has content.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div>
                            <InputLabel :for="`${meta.key}-source`" value="Source Type" />
                            <select
                                :id="`${meta.key}-source`"
                                v-model="form.sections[meta.key].source_type"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option
                                    v-for="option in props.sourceOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors[`sections.${meta.key}.source_type`]" class="mt-2" />
                        </div>

                        <div v-if="form.sections[meta.key].source_type === 'category'">
                            <InputLabel :for="`${meta.key}-category`" value="Category" />
                            <select
                                :id="`${meta.key}-category`"
                                v-model="form.sections[meta.key].category_id"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option :value="null">Choose a category</option>
                                <option
                                    v-for="category in props.categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors[`sections.${meta.key}.category_id`]" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel :for="`${meta.key}-title`" value="Title Override (optional)" />
                            <input
                                :id="`${meta.key}-title`"
                                v-model="form.sections[meta.key].title_override"
                                type="text"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Leave blank to auto-generate"
                            />
                            <InputError :message="form.errors[`sections.${meta.key}.title_override`]" class="mt-2" />
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
