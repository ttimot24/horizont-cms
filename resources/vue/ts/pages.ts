import $ from 'jquery';
import { environment } from './environments/environment';
import axios, { Axios } from  'axios-observable';
import { catchError, throwError } from 'rxjs';

const csrfToken: HTMLElement = document.head.querySelector('meta[name="csrf-token"]') as HTMLElement;
const apiToken: HTMLElement = document.head.querySelector('meta[name="api-token"]') as HTMLElement;

axios.defaults.headers.common['Content-Type'] = "application/json";
axios.defaults.headers.common['Accept'] = "application/json";
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
axios.defaults.headers.common['Authorization'] = 'Bearer ' + apiToken.getAttribute('content');

function readURL(input: HTMLInputElement) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('#preview-i').attr('src', (e?.target?.result as string) || null);
      $("#select-photo").hide();
      $("#selected-image").show();
    }

    reader.readAsDataURL(input.files[0]);
  }
}

export function generateSlug(value?: string): void {
  if (value != "") {
    axios.get(environment.REST_API_BASE + "/slug/generate/" + value)
      .pipe(
        catchError((error: any) => {
          console.error("Error generating slug:", error);
          $("#generatedSlug").html("");
          return throwError(error);
        })
      ).subscribe((response: any) => {
        $("#generatedSlug").html("/" + response.data);
      });
  } else {
    $("#generatedSlug").html("");
  }
}

window.generateSlug = generateSlug;
