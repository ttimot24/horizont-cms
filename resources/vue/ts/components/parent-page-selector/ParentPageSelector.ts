import { defineComponent } from '@vue/composition-api';
import { environment } from '../../environments/environment';
import { Page } from '@smartnowx/hcms-commons';


export default defineComponent({
    name: 'ParentPageSelector',
    props: {
        label: {
            type: String
        },
        languages: {
            type: Object as () => Record<string, string>,
            required: true
        },
        page: {
            type: Object as () => Page | null,
            required: false
        },
        all_pages: {
            type: Array as () => Page[],
            default: []
        }
    },
    data: function () {
        return {
          submenuOpened: 0
        }
    },
    mounted: function () {

        const vm = this;

        console.log("VueJS: ParentPageSelector started");

        if(vm.page != null && vm.page.parent_id != null) {
            vm.submenuOpened = 1;
        }

    },
    methods: {
        currentParentId() {
            return this.currentPage?.parent_id ?? null;
        },
        pagesByLanguage(lang: string): Page[] {
            return this.all_pages.filter((page: Page) => page.language === lang);
        }
    }
});