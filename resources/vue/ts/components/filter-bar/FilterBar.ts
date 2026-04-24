import { defineComponent } from '@vue/composition-api';
import { FilterOption } from './FilterOption';

export default defineComponent({
    name: 'FilterBar',
    props: {
        filterOptions: {
            type: Array as () => FilterOption[],
            required: true
        }
    },
    data: function () { return { 
        selectedField: null as string | null, 
        queryValue: null as string | null 
    } },
    computed: { 
        actualName: function (): string {
            return `filter[${this.selectedField}]`; 
        } 
    },
});