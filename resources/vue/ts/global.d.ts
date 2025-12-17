import { VueConstructor } from "vue";

export {};

declare global {
    interface Window {
        $: any,
        vue: VueConstructor<Vue>;
        CKEDITOR: any,
        bootstrap: any;
        hcms: Vue;
        dragndroporder: () => void;
        generateSlug: (value?: string) => void;
    }
}