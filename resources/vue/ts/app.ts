/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
import * as $ from 'jquery';
import * as bootstrap from 'bootstrap';
import 'bootstrap-fileinput';
import 'select2';
import './dragndrop.ts';
import './pages.ts';
import lang from './lang';

import CKEditor from 'ckeditor4-vue';
import VueI18n from 'vue-i18n';
import VueCompositionAPI from '@vue/composition-api';
import TextEditor from './components/text-editor/TextEditor.vue';
import LockScreen from './components/lock-screen/LockScreen.vue';
import FileManager from './components/file-manager/FileManager.vue';
import CategorySelector from './components/category-selector/CategorySelector.vue';
import ParentPageSelector from './components/parent-page-selector/ParentPageSelector.vue';

window.vue.use(CKEditor);
window.vue.use(VueCompositionAPI);
window.vue.use(VueI18n);

const i18n = new VueI18n({
    locale: window.navigator.language.split('-')[0] || 'en',
    fallbackLocale: 'en',
    messages: lang
});


const hcms = new window.vue({
    name: 'HorizontCMS',
    el: '#hcms',
    i18n,
    data: {

    },
    provide() {
        return {
          bootstrap: bootstrap
        }
    },
    components: {
        TextEditor,
        LockScreen,
        FileManager,
        CategorySelector,
        ParentPageSelector
    },
    created: function(){
        console.info("HorizontCMS started");
        console.log("Available locales:", this.$i18n.availableLocales );
    },
    methods: {
        lock: function(){
            this.$refs.lockscreen.lock();
        },
        setLocale: function(lang: string) {

            if(this.$i18n.availableLocales.includes(lang)){
                this.$i18n.locale = lang;
            }
            console.log("Current locale:", this.$i18n.locale);
        }
    }

});

export default hcms;

window.hcms = hcms;

window.bootstrap = bootstrap;

(window as any).$ = $;
(window as any).jQuery = $;