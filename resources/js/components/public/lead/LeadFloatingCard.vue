<script setup lang="ts">
const props = defineProps<{
    previewMode?: boolean;
    surfaceClass?: string;
}>();
</script>

<template>
    <div class="lead-float">
        <!-- Break out of narrower page containers; align to hero width -->
        <div class="lead-breakout">
            <div class="lead-container">
                <div class="lead-shadow" aria-hidden="true"></div>

                <div class="lead-surface" :class="props.surfaceClass">
                    <div class="lead-top-edge" aria-hidden="true"></div>
                    <slot />
                </div>

                <div
                    class="lead-badge lead-badge-left"
                    aria-hidden="true"
                ></div>
                <div
                    class="lead-badge lead-badge-right"
                    aria-hidden="true"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.lead-float {
    position: relative;
    overflow: visible;
}

/* Full-bleed breakout wrapper so lead blocks can use hero width consistently */
.lead-breakout {
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
}

.lead-container {
    position: relative;
    width: 100%;
    max-width: 80rem; /* max-w-7xl */
    margin: 0 auto;
    padding-left: 1.5rem; /* px-6 */
    padding-right: 1.5rem;
}

@media (min-width: 768px) {
    .lead-container {
        padding-left: 2.5rem; /* md:px-10 */
        padding-right: 2.5rem;
    }
}

.lead-shadow {
    position: absolute;
    inset: 0;
    border-radius: 1.5rem; /* rounded-3xl */
    background: rgba(15, 23, 42, 0.22);
    filter: blur(30px);
    transform: translate3d(14px, 18px, 0) scale(0.98);
    opacity: 0.38;
    pointer-events: none;
}

.lead-surface {
    position: relative;
    z-index: 1;
    border-radius: 1.5rem; /* rounded-3xl */
    background: rgba(255, 255, 255, 0.96);
    overflow: hidden;
}

.lead-top-edge {
    position: absolute;
    inset: 0 0 auto 0;
    height: 26px;
    background: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0.7),
        transparent
    );
    pointer-events: none;
}

.lead-badge {
    position: absolute;
    bottom: -10px;
    height: 12px;
    border-radius: 9999px;
    background: linear-gradient(
        135deg,
        rgba(226, 232, 240, 0.9),
        rgba(241, 245, 249, 0.25)
    );
    box-shadow: 0 10px 18px -14px rgba(15, 23, 42, 0.35);
    opacity: 0.9;
    pointer-events: none;
}

.lead-badge-left {
    left: 28px;
    width: 42px;
}

.lead-badge-right {
    right: 28px;
    width: 26px;
}
</style>
