<template>
    <div v-bind:id="id" class='modal' tabindex='-1'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header modal-header-danger bg-danger'>
                <h4 class='modal-title text-white'>{{ header }}</h4>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                Are you sure you want to delete:
                <b id="content-name">{{ name }}</b>
                ?
            </div>
            <div class='modal-footer'>

                <form id="delete-form" method='POST' v-bind:action="route" v-on:submit.prevent="$emit('delete')">

                <input type="hidden" name="_method" value="delete">

                <button type='submit' id="delete-submit" class='btn btn-danger'><i class="fa fa-trash"
                            aria-hidden="true"></i> {{ delete_text }}</button>
                </form>

  
                <button v-if="cancel" type='button' class='btn btn-default' data-bs-dismiss='modal'>{{ cancel }}</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</template>

<script lang="ts" src="./DeleteModal.ts" />