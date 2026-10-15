import Vue from 'vue';
import VueResource from "vue-resource";
import http from './services/axios-observable';

Vue.use(VueResource);

Vue.config.devtools = true;

const csrfToken: HTMLElement | null = document.head.querySelector('meta[name="csrf-token"]');
const apiToken: HTMLElement | null = document.head.querySelector('meta[name="api-token"]');

http.defaults.headers.common['Content-Type'] = "application/json";
http.defaults.headers.common['Accept'] = "application/json";
http.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken?.getAttribute('content') ?? '';
http.defaults.headers.common['Authorization'] = 'Bearer ' + (apiToken?.getAttribute('content') ?? '');

Vue.prototype.http = http;

Vue.mixin({
    data: function() {
      return {
        get csrfToken(): string | null {
          return csrfToken?.getAttribute('content');
        }
      }
    }
});

window.vue = Vue;
(window as any).Vue = Vue;
