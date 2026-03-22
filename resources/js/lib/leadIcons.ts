import {
    BookOpen,
    CheckCircle2,
    Clock,
    Download,
    MessageSquare,
    Phone,
    ShieldCheck,
    Sparkles,
} from 'lucide-vue-next';
import type { Component } from 'vue';

const ICONS: Record<string, Component> = {
    'book-open': BookOpen,
    download: Download,
    sparkles: Sparkles,

    'shield-check': ShieldCheck,
    clock: Clock,
    'message-square': MessageSquare,
    phone: Phone,
    'check-circle-2': CheckCircle2,
};

export function resolveLeadIcon(iconKey: string | null | undefined): Component | null {
    if (!iconKey) return null;
    return ICONS[iconKey] ?? null;
}
