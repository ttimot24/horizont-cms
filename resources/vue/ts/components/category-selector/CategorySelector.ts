import { defineComponent } from "vue";
import { environment } from "../../environments/environment";
import { BlogpostCategory } from "@smartnowx/hcms-commons";
import { retry, map } from "rxjs";

export default defineComponent({
  name: "CategorySelector",
  props: {
    label: {
      type: String,
    },
    selector: {
      type: String,
      required: true,
    },
    blogpost_categories: {
      type: Array<number>,
      default: [],
    },
  },
  data: function () {
    return {
      categories: [] as BlogpostCategory[],
      selected_categories: [] as number[],
    };
  },
  computed: {
    selectId(): string {
      const s = this.selector;
      return s.startsWith("#") ? s.substring(1) : s;
    },
  },
  mounted: function () {
    const vm = this;

    console.log("VueJS: CategorySelector started");

    vm.selected_categories = vm.blogpost_categories.map(
      (bc: BlogpostCategory) => bc.id,
    );

    vm.http
      .get(environment.REST_API_BASE + "/categories")
      .pipe(
        retry(environment.API_RETRY),
        map((response: any) => response.data.data as BlogpostCategory[]),
      )
      .subscribe((response: BlogpostCategory[]) => {
        vm.categories = response;
        console.log(response);

        vm.$nextTick(() => {
          const $ = (window as any).jQuery;
          if ($) {
            $("#" + vm.selectId).select2({
              theme: "bootstrap-5",
            });
          }
        });
      });
  },
});
