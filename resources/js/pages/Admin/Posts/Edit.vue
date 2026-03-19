<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';

import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import MediaLibraryModal from '@/components/admin/MediaLibraryModal.vue';

import Quill from 'quill';
import 'quill/dist/quill.snow.css';

type OptionRow = {
    id: number;
    name: string;
    slug: string;
};

type PostDto = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
    category_id: number | null;
    tag_ids: number[];
    sources: string | null;

    featured_image_path: string | null;
    featured_image_url: string | null;

    status: 'draft' | 'published';
    published_at: string | null;

    meta_title: string | null;
    meta_description: string | null;
    canonical_url: string | null;
    og_title: string | null;
    og_description: string | null;
    og_image_path: string | null;

    noindex: boolean;
};

const props = defineProps<{
    post: PostDto;
    categories: OptionRow[];
    tags: OptionRow[];
}>();

const page = usePage<any>();
const flashSuccess = computed(() => page.props?.flash?.success ?? '');
const isPublished = computed(() => props.post.status === 'published');

const formatPublishedDate = (value: string | null) => {
    if (!value) return '';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const quillEditorRef = ref<Quill | null>(null);
const editorContainerRef = ref<HTMLDivElement | null>(null);

const form = useForm({
    title: props.post.title ?? '',
    slug: props.post.slug ?? '',
    excerpt: props.post.excerpt ?? '',
    content: props.post.content ?? '',
    sources: props.post.sources ?? '',

    category_id: props.post.category_id ?? null,
    tag_ids: (props.post.tag_ids ?? []) as number[],
    new_category: '',
    new_tags: [] as string[],

    meta_title: props.post.meta_title ?? '',
    meta_description: props.post.meta_description ?? '',
    canonical_url: props.post.canonical_url ?? '',
    og_title: props.post.og_title ?? '',
    og_description: props.post.og_description ?? '',
    og_image_path: props.post.og_image_path ?? '',

    noindex: !!props.post.noindex,

    featured_image: null as File | null,
    featured_image_path: props.post.featured_image_path ?? props.post.featured_image_url ?? '',
    remove_featured_image: false,
});

const displayTitle = computed(() => form.title?.trim() || 'Untitled Post');

const showMediaLibrary = ref(false);
const tagInput = ref('');
const publishing = ref(false);

const featuredImageInputRef = ref<HTMLInputElement | null>(null);
const featuredImagePreviewUrl = ref<string | null>(null);

const featuredImageDisplayUrl = computed(() => {
    return featuredImagePreviewUrl.value || form.featured_image_path || null;
});

const setPreview = (file: File | null) => {
    if (featuredImagePreviewUrl.value) {
        URL.revokeObjectURL(featuredImagePreviewUrl.value);
        featuredImagePreviewUrl.value = null;
    }

    if (file) {
        featuredImagePreviewUrl.value = URL.createObjectURL(file);
    }
};

const openFilePicker = () => {
    featuredImageInputRef.value?.click();
};

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    form.featured_image = file;
    form.featured_image_path = '';
    form.remove_featured_image = false;

    setPreview(file);

    if (file && !form.og_image_path) {
        form.og_image_path = '';
    }
};

const selectFeaturedImage = (path: string) => {
    form.featured_image = null;
    form.featured_image_path = path;
    form.remove_featured_image = false;

    if (featuredImageInputRef.value) {
        featuredImageInputRef.value.value = '';
    }

    setPreview(null);

    if (!form.og_image_path) {
        form.og_image_path = path;
    }
};

const removeFeaturedImage = () => {
    form.featured_image = null;
    form.featured_image_path = '';
    form.remove_featured_image = true;

    if (featuredImageInputRef.value) {
        featuredImageInputRef.value.value = '';
    }

    setPreview(null);
};

const addTag = () => {
    const value = tagInput.value.trim();

    if (!value) return;

    if (!form.new_tags.includes(value)) {
        form.new_tags.push(value);
    }

    tagInput.value = '';
};

const removeNewTag = (index: number) => {
    form.new_tags.splice(index, 1);
};

const beforeUnloadHandler = (e: BeforeUnloadEvent) => {
    if (!form.isDirty) return;
    e.preventDefault();
    e.returnValue = '';
};

onMounted(() => {
    window.addEventListener('beforeunload', beforeUnloadHandler);

    if (editorContainerRef.value) {
        quillEditorRef.value = new Quill(editorContainerRef.value, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['link', 'image'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ header: [1, 2, 3, false] }],
                    ['clean'],
                ],
            },
        });

        quillEditorRef.value.root.innerHTML = form.content;

        quillEditorRef.value.on('text-change', () => {
            form.content = quillEditorRef.value?.root.innerHTML || '';
        });
    }
});

onBeforeUnmount(() => {
    quillEditorRef.value = null;
    window.removeEventListener('beforeunload', beforeUnloadHandler);

    if (featuredImagePreviewUrl.value) {
        URL.revokeObjectURL(featuredImagePreviewUrl.value);
        featuredImagePreviewUrl.value = null;
    }
});

const backToPosts = () => {
    if (form.isDirty && !confirm('You have unsaved changes. Are you sure you want to leave?')) {
        return;
    }

    router.visit(route('admin.posts.index'));
};

const save = () => {
    form.put(route('admin.posts.update', props.post.id), {
        forceFormData: true,
    });
};

const publish = () => {
    publishing.value = true;

    router.post(
        route('admin.posts.publish', props.post.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                publishing.value = false;
            },
        },
    );
};

const unpublish = () => {
    publishing.value = true;

    router.post(
        route('admin.posts.unpublish', props.post.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                publishing.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="`Edit: ${post.title}`" />

    <AdminLayout>
    <div class="p-6">
        <form @submit.prevent="save" class="space-y-6">
            <div class="sticky top-0 z-40 border-b bg-white">
                <div class="space-y-2 py-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ displayTitle }}
                            </div>

                            <div class="mt-1 text-sm text-gray-600">
                                Status:
                                <span class="font-medium">{{ isPublished ? 'Published' : 'Draft' }}</span>

                                <template v-if="isPublished && post.published_at">
                                    <span class="mx-2 text-gray-400">•</span>
                                    <span>Published {{ formatPublishedDate(post.published_at) }}</span>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <PrimaryButton type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving…' : 'Save' }}
                            </PrimaryButton>

                            <SecondaryButton
                                v-if="isPublished"
                                type="button"
                                :disabled="publishing || form.processing"
                                @click="unpublish"
                            >
                                {{ publishing ? 'Working…' : 'Unpublish' }}
                            </SecondaryButton>

                            <SecondaryButton
                                v-else
                                type="button"
                                :disabled="publishing || form.processing"
                                @click="publish"
                            >
                                {{ publishing ? 'Working…' : 'Publish' }}
                            </SecondaryButton>

                            <SecondaryButton type="button" @click="backToPosts">
                                Back to Posts
                            </SecondaryButton>
                        </div>
                    </div>

                    <div v-if="flashSuccess" class="flex items-center gap-2 text-sm text-green-700">
                        <span class="font-semibold">✓</span>
                        <span>{{ flashSuccess }}</span>
                    </div>
                </div>
            </div>

            <section class="space-y-4 rounded-lg border bg-white p-4">
                <div class="text-sm font-semibold text-gray-900">Content</div>

                <div>
                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="slug" value="Slug (optional)" />
                    <TextInput
                        id="slug"
                        v-model="form.slug"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.slug" class="mt-2" />
                    <p class="mt-1 text-xs text-gray-500">
                        If blank, it will be generated from the title.
                    </p>
                </div>

                <div>
                    <InputLabel value="Content" />
                    <div id="editor" ref="editorContainerRef" class="mt-1 h-96"></div>
                    <InputError :message="form.errors.content" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="excerpt" value="Excerpt (optional)" />
                    <textarea
                        id="excerpt"
                        v-model="form.excerpt"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.excerpt" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="sources" value="Sources (optional)" />
                    <textarea
                        id="sources"
                        v-model="form.sources"
                        rows="6"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Plain text references…"
                    />
                    <InputError :message="form.errors.sources" class="mt-2" />
                </div>
            </section>

            <section class="space-y-4 rounded-lg border bg-white p-4">
                <div class="text-sm font-semibold text-gray-900">Media</div>

                <div>
                    <div class="text-sm font-medium text-gray-900">Featured Image</div>

                    <input
                        ref="featuredImageInputRef"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="onFileChange"
                    />

                    <div v-if="featuredImageDisplayUrl" class="mt-3">
                        <img
                            :src="featuredImageDisplayUrl"
                            alt=""
                            class="h-48 w-full max-w-lg rounded-lg border object-cover"
                            loading="lazy"
                            decoding="async"
                        />
                        <div class="mt-2 break-all text-xs text-gray-500">
                            {{ form.featured_image_path || 'New upload selected' }}
                        </div>
                    </div>

                    <div v-else class="mt-3 text-sm text-gray-600">
                        No featured image selected.
                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        <SecondaryButton type="button" @click="showMediaLibrary = true">
                            Choose From Library
                        </SecondaryButton>

                        <SecondaryButton type="button" @click="openFilePicker">
                            Upload New
                        </SecondaryButton>

                        <SecondaryButton
                            v-if="featuredImageDisplayUrl"
                            type="button"
                            @click="removeFeaturedImage"
                        >
                            Remove Image
                        </SecondaryButton>
                    </div>

                    <InputError :message="(form.errors as any).featured_image" class="mt-2" />
                    <InputError :message="(form.errors as any).featured_image_path" class="mt-2" />
                </div>
            </section>

            <section class="space-y-4 rounded-lg border bg-white p-4">
                <div class="text-sm font-semibold text-gray-900">Taxonomy</div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="category_id" value="Category (optional)" />
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option :value="null">— None —</option>
                            <option v-for="c in props.categories" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.category_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="tag_ids" value="Tags (optional)" />
                        <select
                            id="tag_ids"
                            v-model="form.tag_ids"
                            multiple
                            size="6"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option v-for="t in props.tags" :key="t.id" :value="t.id">
                                {{ t.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.tag_ids" class="mt-2" />
                        <InputError :message="(form.errors as any)['tag_ids.*']" class="mt-2" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="new_category" value="New Category" />
                        <TextInput
                            id="new_category"
                            v-model="form.new_category"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Create new category"
                        />
                        <InputError :message="form.errors.new_category" class="mt-2" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="new_tag_input" value="Add Tags" />
                        <TextInput
                            id="new_tag_input"
                            v-model="tagInput"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Type tag and press Enter"
                            @keydown.enter.prevent="addTag"
                        />

                        <div class="mt-2 flex flex-wrap gap-2">
                            <span
                                v-for="(tag, index) in form.new_tags"
                                :key="index"
                                class="inline-flex items-center gap-2 rounded bg-gray-200 px-2 py-1 text-xs"
                            >
                                {{ tag }}
                                <button
                                    type="button"
                                    class="text-gray-600 hover:text-gray-900"
                                    @click="removeNewTag(index)"
                                >
                                    ×
                                </button>
                            </span>
                        </div>

                        <InputError :message="form.errors.new_tags" class="mt-2" />
                        <InputError :message="(form.errors as any)['new_tags.*']" class="mt-2" />
                    </div>
                </div>
            </section>

            <section class="space-y-4 rounded-lg border bg-white p-4">
                <div class="text-sm font-semibold text-gray-900">SEO</div>

                <div>
                    <InputLabel for="meta_title" value="Meta Title (optional)" />
                    <TextInput
                        id="meta_title"
                        v-model="form.meta_title"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.meta_title" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="meta_description" value="Meta Description (optional)" />
                    <textarea
                        id="meta_description"
                        v-model="form.meta_description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.meta_description" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="canonical_url" value="Canonical URL (optional)" />
                    <TextInput
                        id="canonical_url"
                        v-model="form.canonical_url"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.canonical_url" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="og_title" value="OG Title (optional)" />
                    <TextInput
                        id="og_title"
                        v-model="form.og_title"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.og_title" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="og_description" value="OG Description (optional)" />
                    <textarea
                        id="og_description"
                        v-model="form.og_description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.og_description" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="og_image_path" value="OG Image Path (optional)" />
                    <TextInput
                        id="og_image_path"
                        v-model="form.og_image_path"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.og_image_path" class="mt-2" />
                </div>

                <div class="flex items-center gap-2">
                    <input
                        id="noindex"
                        v-model="form.noindex"
                        type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label for="noindex" class="text-sm text-gray-700">Noindex</label>
                </div>

                <InputError :message="form.errors.noindex" class="mt-2" />
            </section>

            <div class="flex items-center gap-3">
                <PrimaryButton type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving…' : 'Save' }}
                </PrimaryButton>
            </div>
        </form>
        </div>

        <MediaLibraryModal
            :open="showMediaLibrary"
            :selected-path="form.featured_image_path"
            default-folder="blog"
            @update:open="showMediaLibrary = $event"
            @select="selectFeaturedImage"
        />
    </AdminLayout>
</template>
