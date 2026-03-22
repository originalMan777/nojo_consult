export type LeadBlockType = 'resource' | 'service' | 'offer';

export type LeadBlockRenderModel = {
    leadBoxId: number;
    type: LeadBlockType;
    title: string;
    shortText: string | null;
    buttonText: string | null;
    iconKey: string | null;
    content: Record<string, any>;
    context: {
        slotKey: string;
        pageKey: string;
    };
};
