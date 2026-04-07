<template>
    <div class='modal fade' id='lock_screen' ref="lock_screen" role='dialog' v-on:keydown.enter="unlock"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby='myModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header '>
                    <h2 class='text-primary color-primary'><i class="fa fa-lock"></i> Screen is locked</h2>
                </div>
                <div class='modal-body text-center'>

                    <div>
                        <img v-bind:src="user.image? 'storage/images/users/' + user.image : 'resources/images/icons/profile.png'" class='img-thumbnail'
                            style="max-height:15rem; object-fit:cover;" />
                        <br>
              
                        <b class="fs-5 m-3">{{ user.username }}</b>
                    </div>


                    <div class='form-group mt-5'>
                        <label for='pwd' class="form-label">Password</label>
                        <input type='password' class='form-control w-100' id='lock_pwd' v-model="password" ref="password_field" required />
                        <span class="invalid-feedback" role="alert">
                            <strong>Wrong password</strong>
                        </span>
                    </div>


                </div>
                <div class='modal-footer'>
                    <button type='button' id='unlock_button' class='btn btn-primary'
                        v-on:click="unlock">Unlock</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" src="./LockScreen.ts" />