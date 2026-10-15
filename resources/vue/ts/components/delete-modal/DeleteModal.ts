import { defineComponent } from 'vue';

export default defineComponent({
    name: 'DeleteModal',
    props: {
      id: {
        type: String,
        required: true
      },
      route: {
        type: String
      },
      delete_text: {
        type: String,
      },
      header: {
        type: String,
      },
      name: {
        type: String,
      },
      cancel: {
        type: String,
      }
    }
});