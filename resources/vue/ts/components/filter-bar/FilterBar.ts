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
        selectedField: this.filterOptions.length > 0 ? this.filterOptions[0].value : null,
        queryValue: null as string | null 
    } },
    computed: { 
        actualName: function (): string {
            return `filter[${this.selectedField}]`; 
        },
        selectedFieldType: function (): string | undefined {
            const selectedOption = this.filterOptions.find((option: FilterOption) => option.value === this.selectedField);
            return selectedOption ? selectedOption.type : 'text';
        }
    } 
});