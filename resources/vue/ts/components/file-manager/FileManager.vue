<template>
    <div id="filemanager">

        <section class='container'>

                <div class="container py-0">

                    <div class="row">

                        <div class='panel panel-default col-12 col-md-2 bg-dark p-3' style="min-height:600px;">
                            <h4 class="p-2 bg-dark text-white">Disks</h4>
                            <ul class="list-group">

                                <!-- v-on:click.prevent="open('{{ isset($value['root']) ? basename($value['root']) : '' }}', false);" -->
                                <a href="#" v-for="(disk) in disks" v-on:click.prevent="switchDisk(disk)">
                                    <li class="list-group-item bg-dark text-white" :class="disk===currentDisk? 'active' : '' ">{{ disk }}</li>
                                </a>

                            </ul>
                        </div>

                        <div class="panel panel-default col-12 col-md-10 bg-dark">
                            <div class="panel-body">
                                <div class="row p-0 m-0">
                                    <div class="col-md-2 m-0 p-0">
                                        <nav aria-label="breadcrumb p-0 m-0">
                                            <ol class="breadcrumb bg-dark p-0 pt-3 m-0">
                                                <li class="breadcrumb-item"><a href="storage"
                                                        v-on:click.prevent="open('', false); ">{{ currentDisk }}: storage</a></li>
                                                <li class="breadcrumb-item" v-for="(  bcrumb  ) in   breadcrumb  "><a
                                                        :href=" bcrumb.link "
                                                        v-on:click.prevent=" open(bcrumb.link, false); ">{{ bcrumb.text
                                                        }}</a>
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                    <div class="col-md-8">
                                        <div class='col text-end mt-2'>
                                            <div class="row">
                                                <div class="col-md-4 offset-md-3 col-sm-7 col-xs-7 text-end">
                                                    <input type="text" v-model="filter" class="form-control input-sm" id="filter"
                                                        placeholder="Filter">
                                                </div>
                                                <div class="col text-end">
                                                    <a class='btn btn-primary btn-sm mr-2' data-bs-toggle='modal' data-bs-backdrop='static'
                                                        data-bs-target='.upload_file_to_storage'>
                                                        <i class="fa fa-upload" aria-hidden="true"></i> Upload</a>
                                                    <a v-for=" type in  newTypes "  class='btn btn-sm btn-primary me-1' data-bs-toggle='modal' data-bs-backdrop='static'
                                                        :data-bs-target="'.new_' + type">
                                                        <i :class="'fa fa-' + type" aria-hidden="true"></i> Create {{ type }}
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end pt-3 pr-3">
                                        <div class="row">
                                            <div class="col text-white ">All: {{ folders.length + files.length }}</div>
                                            <div class="col">
                                                <a href="a" v-on:click.prevent=" open(currentDirectory, false); "><i
                                                        class="fa fa-refresh" onclick="$(this).addClass('fa-spin');"
                                                        aria-hidden="true" style="font-size: 22px;"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="workspace" class="col-md-12 py-3 pe-5">

                                    <div v-if="openError" class="alert alert-danger d-flex align-items-center" role="alert">
                                        <i class="fa-solid fa-circle-exclamation me-3"></i>
                                        <div>
                                            Could not open folder: {{openError.error}}
                                        </div>
                                    </div>

                                    <div v-if="!openError" class="row text-white">
                                        <div class='folder col-md-2 col-sm-4 col-xs-4 text-center text-white'
                                            v-for="  folder   in   folders  " :id=" folder " v-on:click=" select(folder) "
                                            v-on:dblclick=" open(folder); ">

                                            <div class="file-nav text-end">
                                                <a class="me-1" v-on:click=" renameModal(folder) "><i
                                                        class="fa fa-pencil"></i></a>
                                                <a class="me-1" v-on:click=" deleteModal(folder) "><i
                                                        class="fa fa-trash"></i></a>
                                            </div>

                                            <div class='row my-2'>
                                               <i class="fa-solid fa-folder-closed text-warning" style="font-size: 6rem;"></i>
                                            </div>
                                            <b>{{ folder }}</b>
                                        </div>

                                        <div v-for="  file   in  files " class='file col-md-2 col-sm-4 col-xs-4 text-center'
                                            :id=" file " v-on:click="
                                                mode === 'embed' ?
                                                    returnFileUrl('storage/' + currentDirectory + '/' + file) :
                                                    select(file)
                                            ">
                                            <div class="file-nav text-end">
                                                <a class="me-1" v-on:click="shareLink(file)"><i
                                                    class="fa fa-link"></i></a>
                                                <a class="me-1" v-on:click="renameModal(file)"><i class="fa fa-pencil"
                                                        aria-hidden="true"></i></a>
                                                <a class="me-1" :href=" 'storage/' + currentDirectory + '/' + file "><i
                                                        class="fa fa-download"></i></a>
                                                <a class="me-1" v-on:click="deleteModal(file)"><i
                                                        class="fa fa-trash"></i></a>
                                            </div>
                                            <div class='row my-2'>
                                                <img class="w-100" v-if="isKnownExtension(file)"
                                                :src=" 'storage/' + currentDirectory + '/' + file " />
                                                <i class="fa-regular fa-file-lines" v-else style="font-size: 6rem;"></i>
                                            </div>
                                            <b>{{ file }}</b>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>
        </section>



    <div v-for=" type in  newTypes "  :class="'modal new_' + type" :id="'new_' + type" tabindex='-1' role='dialog' :aria-labelledby="'new_' + type" aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header modal-header-primary bg-primary'>
                    <h4 class='modal-title text-white'>Create new {{ type }}</h4>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>

                <form :action="'admin/file-manager/new-' + type" method='POST' enctype='multipart/form-data' v-on:submit.prevent=" createNew(event, type) ">
                    <div class='modal-body'>
                        <div class='form-group'>
                            <div class='form-group'>
                                <label for='title' class="form-label">Name</label>
                                <input type='text' class='form-control' :name="'new_' + type + '_name'"
                                    :placeholder="'Enter ' + type + ' name'" required>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='submit' class='btn btn-primary'>Create</button>
                        <button type='button' class='btn btn-default' data-bs-dismiss='modal'>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class='modal rename_modal' id='rename_sample' tabindex='-1' role='dialog' aria-labelledby='rename_file'
        aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header modal-header-primary'>
                    <h4 class='modal-title'>Rename</h4>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>

                <form action='admin/file-manager/rename' method='POST' v-on:submit.prevent=" renameFile ">
                    <div class='modal-body'>

                        <div class='form-group'>
                            <div class='form-group'>
                                <label for='title' class="form-label">Selected</label>
                                <input type='text' class='form-control' name='old_name' id="selected" disabled>
                            </div>
                        </div>

                        <div class='form-group'>
                            <div class='form-group'>
                                <label for='title' class="form-label">New name</label>
                                <input type='text' class='form-control' name='new_name' id="selected" required>
                            </div>
                        </div>


                    </div>
                    <div class='modal-footer'>
                        <button type='submit' class='btn btn-primary'>Rename</button>
                        <button type='button' class='btn btn-default' data-bs-dismiss='modal'>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class='modal upload_file_to_storage' id='upload_file_to_storage' tabindex='-1' role='dialog'
        aria-labelledby='upload_file_to_storage' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header modal-header-primary bg-primary'>
                    <h4 class='modal-title text-white'>Upload file</h4>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>

                <form action="admin/filemanager" method='POST' enctype='multipart/form-data'
                    v-on:submit.prevent="upload">
                    <div class='modal-body'>


                        <div class='form-group'>
                            <label for='file' class="form-label">Upload file</label>
                            <input name='up_file[]' id='input-2' type='file' class='file' multiple='true'
                                data-show-upload='false' data-show-caption='true' required>
                        </div>

                    </div>
                    <div class='modal-footer'>
                        <button type='submit' class='btn btn-primary'>Upload</button>
                        <button type='button' class='btn btn-default'
                            data-bs-dismiss='modal'>Cancel</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <delete-modal :id="'delete_sample'" :route="'admin/file-manager/destroy'" :header="'Are you sure you want to delete?'" :name="'[dir_name_sample]'" :delete_text="'Delete'" :cancel="'Cancel'" ref="deletemodal"></delete-modal>

    </div>

</template>

<script lang="ts" src="./FileManager.ts"/>