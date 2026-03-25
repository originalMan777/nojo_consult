<template>
    <div class="lead-accent" aria-hidden="true">
        <span class="lead-accent__halo"></span>
        <span class="lead-accent__frame">
            <img class="lead-accent__logo" src="/NOJO-FAVICON.jpg" alt="" />
        </span>
    </div>
</template>

<style scoped>
.lead-accent {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    pointer-events: none;
}

.lead-accent__halo {
    position: absolute;
    inset: 0.2rem;
    border-radius: 9999px;
    background: radial-gradient(
        circle,
        rgba(165, 144, 100, 0.3) 0%,
        rgba(165, 144, 100, 0.06) 62%,
        transparent 100%
    );
    animation: leadAccentGlow 4.8s ease-in-out infinite;
}

.lead-accent__frame {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.94);
    box-shadow:
        0 10px 24px rgba(29, 63, 31, 0.12),
        inset 0 0 0 1px rgba(29, 63, 31, 0.08);
    animation: leadAccentFloat 4.8s cubic-bezier(0.22, 1, 0.36, 1) infinite;
}

.lead-accent__logo {
    width: 1.4rem;
    height: 1.4rem;
    border-radius: 9999px;
    object-fit: cover;
}

@keyframes leadAccentFloat {
    0%,
    100% {
        transform: translate3d(0, 0, 0) scale(1);
    }
    45% {
        transform: translate3d(0, -1px, 0) scale(1.03);
    }
    60% {
        transform: translate3d(0, 1px, 0) scale(0.995);
    }
}

@keyframes leadAccentGlow {
    0%,
    100% {
        opacity: 0.45;
        transform: scale(0.94);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.06);
    }
}

@media (prefers-reduced-motion: reduce) {
    .lead-accent__halo,
    .lead-accent__frame {
        animation: none;
    }
}
</style>
