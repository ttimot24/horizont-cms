<template>
    <div id="filemanager">

        <section class="container">

            <div class="container py-0">

                <div class="row">

                    <div class="panel panel-default col-12 col-md-2 bg-dark p-3" style="min-height: 600px;">
                        <h4 class="p-2 bg-dark text-white">{{ $t('file.disks') }}</h4>

                        <ul class="list-group">
                            <button type="button" v-for="disk in disks" :key="disk"
                                class="list-group-item list-group-item-action text-white text-start"
                                style="--bs-list-group-bg: transparent;"
                                :class="disk === currentDisk ? 'selected' : ''"
                                v-on:click="switchDisk(disk)">
                                <i class="fa-solid fa-hard-drive me-2" aria-hidden="true"></i>{{ disk }}
                            </button>
                        </ul>
                    </div>

                    <div class="panel panel-default col-12 col-md-10 bg-dark">

                        <div class="panel-body position-relative" v-on:dragover.prevent="onDragOver"
                            v-on:dragleave.prevent="onDragLeave" v-on:drop.prevent="onDrop">

                            <div class="row p-0 m-0 align-items-center">

                                <div class="col-12 col-lg-5 py-2">
                                    <nav :style="breadcrumbDividerStyle" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a href="storage" v-on:click.prevent="open('', false)">
                                                    {{ currentDisk }}: storage
                                                </a>
                                            </li>
                                            <li class="breadcrumb-item" v-for="part in breadcrumb" :key="part.link">
                                                <a v-if="part.link !== currentDirectory" :href="part.link"
                                                    v-on:click.prevent="open(part.link, false)">
                                                    {{ part.text }}
                                                </a>
                                                <a v-else>{{ part.text }}</a>
                                            </li>
                                        </ol>
                                    </nav>
                                </div>

                                <div class="col-12 col-lg-7 py-2">
                                    <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">

                                        <div class="input-group input-group-sm w-auto">
                                            <span class="input-group-text bg-secondary text-white border-secondary">
                                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                            </span>
                                            <input type="search" class="form-control" v-model="filter"
                                                :placeholder="$t('actions.search')" :aria-label="$t('actions.search')">
                                        </div>

                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn"
                                                :class="view === 'grid' ? 'btn-primary' : 'btn-outline-light'"
                                                :title="$t('file.grid_view')" :aria-label="$t('file.grid_view')"
                                                v-on:click="setView('grid')">
                                                <i class="fa-solid fa-table-cells" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn"
                                                :class="view === 'list' ? 'btn-primary' : 'btn-outline-light'"
                                                :title="$t('file.list_view')" :aria-label="$t('file.list_view')"
                                                v-on:click="setView('list')">
                                                <i class="fa-solid fa-list" aria-hidden="true"></i>
                                            </button>
                                        </div>

                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-light"
                                                :title="$t('file.up_one_level')" :aria-label="$t('file.up_one_level')"
                                                :disabled="currentDirectory === ''" v-on:click="up">
                                                <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-light"
                                                :title="$t('file.refresh')" :aria-label="$t('file.refresh')"
                                                v-on:click="refresh">
                                                <i class="fa-solid fa-refresh" :class="{ 'fa-spin': loading }"
                                                    aria-hidden="true"></i>
                                            </button>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-primary" v-on:click="openUpload">
                                            <i class="fa-solid fa-upload me-1" aria-hidden="true"></i>{{ $t('actions.upload') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            v-on:click="openCreate('folder')">
                                            <i class="fa-solid fa-folder-plus me-1" aria-hidden="true"></i>{{ $t('file.new_folder') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" v-on:click="openCreate('file')">
                                            <i class="fa-solid fa-file-circle-plus me-1" aria-hidden="true"></i>{{ $t('file.new_file') }}
                                        </button>

                                    </div>
                                </div>

                            </div>

                            <div v-if="flash" class="alert alert-dismissible mb-0" :class="'alert-' + flash.type"
                                role="alert">
                                {{ flash.text }}
                                <button type="button" class="btn-close" v-on:click="flash = null"
                                    :aria-label="$t('actions.close')"></button>
                            </div>

                            <div v-if="openError" class="alert alert-danger mb-0" role="alert">
                                {{ openError.error || $t('file.action_failed') }}
                            </div>

                            <div class="row m-0">

                                <div :class="preview === null ? 'col-12' : 'col-12 col-lg-8'">

                                    <div class="row text-white m-0">

                                        <div v-if="loading && totalCount === 0" class="col-12 text-center text-white-50 py-5">
                                            <i class="fa-solid fa-spinner fa-spin fa-3x mb-3" aria-hidden="true"></i>
                                            <p class="mb-0">{{ $t('file.loading') }}</p>
                                        </div>

                                        <div v-else-if="itemCount === 0" class="col-12 text-center text-white-50 py-5">
                                            <i class="fa-solid fa-folder-open fa-3x mb-3" aria-hidden="true"></i>
                                            <p class="mb-1">
                                                {{ filter ? $t('file.no_matches') : $t('file.empty_folder') }}
                                            </p>
                                            <p class="small mb-0">{{ $t('file.empty_hint') }}</p>
                                        </div>

                                        <template v-else-if="view === 'grid'">
                                            <div v-for="entry in visibleEntries" :key="entry.type + '-' + entry.name"
                                                class="text-center text-white"
                                                :class="[tileClass, entry.type === 'dir' ? 'folder' : 'file', { selected: selected === entry.name }]"
                                                v-on:click="selectEntry(entry)" v-on:dblclick="openEntry(entry)">

                                                <div class="file-nav text-end">
                                                    <a class="me-1 link-light" href="#"
                                                        :title="entry.type === 'dir' ? $t('file.open') : $t('file.preview')"
                                                        v-on:click.stop.prevent="openEntry(entry)">
                                                        <i class="fa-solid" aria-hidden="true"
                                                            :class="entry.type === 'dir' ? 'fa-folder-open' : 'fa-eye'"></i>
                                                    </a>
                                                    <a class="me-1 link-light" href="#" :title="$t('file.rename')"
                                                        v-on:click.stop.prevent="openRename(entry.name)">
                                                        <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    <a v-if="entry.type === 'file'" class="me-1 link-light"
                                                        :href="storageUrl(entry.name)" :download="entry.name"
                                                        :title="$t('file.download')">
                                                        <i class="fa-solid fa-download" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="me-1 link-light" href="#" :title="$t('actions.delete')"
                                                        v-on:click.stop.prevent="deleteModal(entry.name)">
                                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </div>

                                                <div class="row my-2 align-items-center" style="min-height: 100px;">
                                                    <img class="w-100" v-if="entry.kind === 'image'"
                                                        :src="storageUrl(entry.name)" :alt="entry.name" loading="lazy">
                                                    <i class="fa-solid fa-folder-closed text-warning"
                                                        v-else-if="entry.type === 'dir'" style="font-size: 4.8rem;"
                                                        aria-hidden="true"></i>
                                                    <i class="fa-regular fa-file-lines" v-else
                                                        style="font-size: 4.8rem;" aria-hidden="true"></i>
                                                </div>

                                                <b class="d-block text-truncate" :title="entry.name">{{ entry.name }}</b>
                                            </div>
                                        </template>

                                        <div v-else class="col-12">
                                            <table class="table table-dark table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">
                                                            <a class="link-light text-decoration-none" href="#"
                                                                v-on:click.prevent="toggleSort">
                                                                {{ $t('file.name') }}
                                                                <i class="fa-solid" :class="sortIcon()"
                                                                    aria-hidden="true"></i>
                                                            </a>
                                                        </th>
                                                        <th scope="col" class="text-end">{{ $t('actions.options') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="entry in visibleEntries"
                                                        :key="entry.type + '-' + entry.name"
                                                        :class="{ selected: selected === entry.name }"
                                                        v-on:click="selectEntry(entry)" v-on:dblclick="openEntry(entry)">
                                                        <td>
                                                            <i class="fa-solid fa-folder-closed text-warning me-2"
                                                                v-if="entry.type === 'dir'" aria-hidden="true"></i>
                                                            <i class="fa-solid fa-file-image me-2"
                                                                v-else-if="entry.kind === 'image'" aria-hidden="true"></i>
                                                            <i class="fa-regular fa-file-lines me-2" v-else
                                                                aria-hidden="true"></i>
                                                            {{ entry.name }}
                                                        </td>
                                                        <td class="text-end text-nowrap">
                                                            <a class="me-2 link-light" href="#"
                                                                :title="entry.type === 'dir' ? $t('file.open') : $t('file.preview')"
                                                                v-on:click.stop.prevent="openEntry(entry)">
                                                                <i class="fa-solid" aria-hidden="true"
                                                                    :class="entry.type === 'dir' ? 'fa-folder-open' : 'fa-eye'"></i>
                                                            </a>
                                                            <a class="me-2 link-light" href="#"
                                                                :title="$t('file.rename')"
                                                                v-on:click.stop.prevent="openRename(entry.name)">
                                                                <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                                            </a>
                                                            <a v-if="entry.type === 'file'" class="me-2 link-light"
                                                                :href="storageUrl(entry.name)" :download="entry.name"
                                                                :title="$t('file.download')">
                                                                <i class="fa-solid fa-download" aria-hidden="true"></i>
                                                            </a>
                                                            <a class="me-2 link-light" href="#"
                                                                :title="$t('actions.delete')"
                                                                v-on:click.stop.prevent="deleteModal(entry.name)">
                                                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                                <aside class="col-12 col-lg-4 text-white" v-if="preview"
                                    :aria-label="$t('file.preview')">
                                    <div class="border-start border-secondary h-100 ps-3">

                                        <div class="d-flex align-items-center justify-content-between gap-2 py-2">
                                            <b class="d-inline-block text-truncate" :title="preview.name">
                                                <i class="fa-solid fa-file-image me-1" v-if="preview.kind === 'image'"
                                                    aria-hidden="true"></i>
                                                <i class="fa-regular fa-file-lines me-1" v-else
                                                    aria-hidden="true"></i>{{ preview.name }}
                                            </b>

                                            <div class="text-nowrap">
                                                <a class="me-2 link-light" :href="storageUrl(preview.name)"
                                                    :download="preview.name" :title="$t('file.download')">
                                                    <i class="fa-solid fa-download" aria-hidden="true"></i>
                                                </a>
                                                <a class="me-2 link-light" href="#" :title="$t('file.copy_link')"
                                                    v-on:click.prevent="copyLink(preview.name)">
                                                    <i class="fa-solid fa-link" aria-hidden="true"></i>
                                                </a>
                                                <a class="me-2 link-light" href="#" :title="$t('file.rename')"
                                                    v-on:click.prevent="openRename(preview.name)">
                                                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                                </a>
                                                <a class="me-2 link-light" href="#" :title="$t('actions.delete')"
                                                    v-on:click.prevent="deleteModal(preview.name)">
                                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                </a>
                                                <a class="link-light" href="#" :title="$t('actions.close')"
                                                    v-on:click.prevent="closePreview()">
                                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="overflow-auto py-2" style="max-height: 60vh;">
                                            <div v-if="preview.loading" class="text-center text-white-50 py-5">
                                                <i class="fa-solid fa-spinner fa-spin fa-2x mb-2"
                                                    aria-hidden="true"></i>
                                                <p class="mb-0">{{ $t('file.loading') }}</p>
                                            </div>

                                            <img v-else-if="preview.kind === 'image'" class="w-100"
                                                :src="storageUrl(preview.name)" :alt="preview.name">

                                            <pre v-else-if="preview.content !== null"
                                                class="text-white p-2 mb-0">{{ preview.content }}</pre>

                                            <div v-else class="text-center text-white-50 py-5">
                                                <i class="fa-solid fa-eye-slash fa-2x mb-2" aria-hidden="true"></i>
                                                <p class="mb-0">{{ preview.message || $t('file.no_preview') }}</p>
                                            </div>
                                        </div>

                                    </div>
                                </aside>

                            </div>

                            <div v-if="dragging"
                                class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-primary bg-opacity-75 text-white">
                                <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2" aria-hidden="true"></i>
                                <span>{{ $t('file.drop_files') }}</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </section>

        <div class="modal" ref="createModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-header-primary bg-primary">
                        <h4 class="modal-title text-white">
                            {{ createType === 'folder' ? $t('file.new_folder') : $t('file.new_file') }}
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            :aria-label="$t('actions.close')"></button>
                    </div>

                    <form v-on:submit.prevent="createNew">
                        <div class="modal-body">
                            <div v-if="createError" class="alert alert-danger">{{ createError }}</div>

                            <label class="form-label" for="fm-create-name">{{ $t('file.name') }}</label>
                            <input id="fm-create-name" ref="createInput" type="text" class="form-control"
                                v-model="createName" :placeholder="$t('file.new_name')" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ $t('actions.create') }}</button>
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ $t('actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal" ref="renameModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-header-primary bg-primary">
                        <h4 class="modal-title text-white">{{ $t('file.rename') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            :aria-label="$t('actions.close')"></button>
                    </div>

                    <form v-on:submit.prevent="renameFile">
                        <div class="modal-body">
                            <div v-if="renameError" class="alert alert-danger">{{ renameError }}</div>

                            <p class="text-muted">
                                {{ $t('file.current_name') }}: <b>{{ selected }}</b>
                            </p>

                            <label class="form-label" for="fm-rename-name">{{ $t('file.new_name') }}</label>
                            <input id="fm-rename-name" ref="renameInput" type="text" class="form-control"
                                v-model="renameName" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ $t('file.rename') }}</button>
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ $t('actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal" ref="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-header-primary bg-primary">
                        <h4 class="modal-title text-white">{{ $t('file.upload_files') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            :aria-label="$t('actions.close')"></button>
                    </div>

                    <form v-on:submit.prevent="uploadSelected">
                        <div class="modal-body">
                            <div v-if="uploadError" class="alert alert-danger">{{ uploadError }}</div>

                            <label class="form-label" for="fm-upload-input">{{ $t('file.upload_files') }}</label>
                            <input id="fm-upload-input" ref="uploadInput" name="up_file[]" type="file"
                                class="form-control" multiple v-on:change="onFileSelect">

                            <div v-if="uploading" class="progress mt-3">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    :style="{ width: uploadProgress + '%' }" :aria-valuenow="uploadProgress"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" :disabled="uploading">
                                <i class="fa-solid fa-upload me-1" aria-hidden="true"></i>{{ $t('actions.upload') }}
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                :disabled="uploading">{{ $t('actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <delete-modal ref="deletemodal" id="filemanager-delete-modal" :route="'admin/file-manager/destroy'"
            :header="$t('actions.are_you_sure')" :name="selectedForDelete || ''" :delete_text="$t('actions.delete')"
            :cancel="$t('actions.cancel')" v-on:delete="deleteFile"></delete-modal>

    </div>
</template>

<script lang="ts" src="./FileManager.ts"/>
