import { Modal } from 'bootstrap';
import { defineComponent } from '@vue/composition-api';
import { User } from '@smartnowx/hcms-commons';
import { AxiosResponse, AxiosError } from 'axios';
import { environment } from '../../environments/environment';
import { catchError, of } from 'rxjs';

export default defineComponent({
    name: 'LockScreen',
    props: {
        user: {
            type: Object as () => User
        }
    },
    data: function(){
        return {
            password: ''
        }
    },
    mounted: function () {

        const vm = this;

        vm.modal = new Modal(vm.$refs.lock_screen);

        if (typeof (Storage) !== undefined) {

            if (localStorage.locksession != null) {
                if (localStorage.locksession == 'true') {
                    vm.modal.show();
                }
                else if (localStorage.locksession == 'false') {
                    vm.modal.hide();
                }
            }
        }

    },
    methods: {
        lock: function (): void {
            const vm = this;
            vm.modal = new Modal(document.getElementById('lock_screen') as HTMLElement);
            vm.modal.show();
            localStorage.locksession = 'true';
            vm.invalid_pw = false;
        },
        unlock: function (): void {
            const vm = this;

            console.log(vm);

            let password_field: any = $("#lock_pwd");

            this.http.post(environment.REST_API_BASE+'/lock-up',
                {
                    id: this.user.id,
                    password: (this.password) as string,
                }
            ).pipe(
                catchError((error: AxiosError) => {
                    password_field = null;
                    console.log(error);
                    return of(null);
                })
            )
            .subscribe((response: AxiosResponse) => {

                if (response.data) {

                    vm.modal.hide();
                    localStorage.locksession = 'false';

                } else {
                    password_field.addClass('is-invalid');
                }

                password_field = null;

            });

        }
    }
});