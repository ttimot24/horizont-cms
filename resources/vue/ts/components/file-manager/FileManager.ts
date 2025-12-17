import { defineComponent } from '@vue/composition-api';
import DeleteModal from '../delete-modal/DeleteModal.vue';
import { environment } from '../../environments/environment';
import { FileManagerResponse } from '../../model/FileManagerResponse';
import { catchError, of, map, retry, throwError } from 'rxjs';

export default defineComponent({
    name: 'FileManager',
    components: {
        DeleteModal
    },
    inject: ['bootstrap'],
    props: {
        directory: {
            type: String,
            default: ''
        },
        disks: {
            type: Array<string>,
            default: []
        },
        mode: {
            type: String,
            default: 'standalone'
        }
    },
    data: function () {
        return {
            previousDirectory: null,
            currentDirectory: this.directory,
            drivers: [],
            folders: [],
            files: [],
            knownFileExtensions: ['jpg', 'png', 'jpeg'],
            messages: [],
            filter: null,
            selected: null,
            currentDisk: 'local',
            openError: null,
        }
    },
    mounted: function () {

        const vm = this;

        console.log("VueJS: FileManager started");
        console.log('Opening: ' + vm.currentDirectory);
        vm.open(vm.currentDirectory, false);
        console.log('Directory: ' + vm.currentDirectory);


        vm.modalRename = vm.getModal("rename_sample");
        vm.modalUpload = vm.getModal("upload_file_to_storage");
        vm.modalNewFolder = vm.getModal("new_folder");
        vm.modalDelete = vm.getModal("delete_sample");

        $('#delete-form').on('submit', (event: Event) => { event.preventDefault(); this.deleteFile(); });
    },
    watch: {
        filter: function (filter: string) {
            var vm = this;

            if (filter != null && filter != "") {
                vm.folders = vm.folders.filter((folder: string) => folder.includes(filter));
                vm.files = vm.files.filter((folder: string) => folder.includes(filter));
            } else {
                vm.open(vm.currentDirectory, false);
            }
        }
    },
    computed: {
        breadcrumb: function (): {
            text: any,
            link: string
        }[] {
            var vm = this;

            var here = vm.currentDirectory.split('/');

            var parts = [];

            for (var i = 0; i < here.length; i++) {
                var part = here[i];
                var text = part;
                var link = '' + here.slice(0, i + 1).join('/');
                parts.push({ "text": text, "link": link });
            }

            return parts;
        }
    },
    methods: {
        getModal: function (id: string): bootstrap.Modal {
            return (new this.bootstrap.Modal(document.getElementById(id) || {} as HTMLElement));
        },
        switchDisk: function (disk: string): void {
            this.currentDisk = disk;
            this.currentDirectory = '';
            this.open('', false);
        },
        select: function (file: string): void {
            var vm = this;

            vm.selected = (event?.currentTarget as any).id;
            $(".file").removeClass('selected');
            $(".folder").removeClass('selected');
            $((event?.currentTarget as any)).addClass('selected');
            console.log('Selected file: ' + vm.selected);
        },
        open: function (folder: string, useCurrent = true): void {

            const vm = this;

            vm.openError = null;

            if (useCurrent) {
                var folderToOpen = vm.currentDirectory + '/' + folder;
            } else {
                var folderToOpen = folder;
            }

            console.debug(vm);

            this.http.get(environment.REST_API_BASE + '/file-manager?disk='+vm.currentDisk+'&path='+folderToOpen)
            .pipe(
                retry(environment.API_RETRY),
                map((response: any) => response.data as FileManagerResponse),
                catchError((error: any) => {
                    console.error(error);
                    this.openError = error.response?.data || { error: 'Unknown error' };
                    return throwError(error);
                })
            )
            .subscribe((data: FileManagerResponse) => {
                console.debug(vm);
                vm.previousDirectory = vm.currentDirectory;
                vm.currentDirectory = data.current_dir;
                vm.folders = [];
                vm.files = [];

                console.log(data);

                if (data.dirs && typeof data.dirs !== undefined && data.dirs.length > 0) {

                    data.dirs.forEach(function (each: string) {
                        vm.folders.push(each);
                    });
                }

                if (data.files && typeof data.files !== undefined && data.files.length > 0) {


                    data.files.forEach(function (each: string) {
                        vm.files.push(each);
                    });

                }

                $('.fa-refresh').removeClass('fa-spin');
            });

        },
        newFolder: function (event: any): void {

            var vm = this;

            var dirPath = vm.currentDirectory;
            var folderName = $('[name="new_folder_name"]').val();


            $.post(event.target.action,
                {
                    _token: vm.csrfToken,
                    dir_path: dirPath,
                    new_folder_name: folderName
                },
                function (data: any) {
                    if (typeof data.success !== undefined) {

                        console.log("Dir created: " + dirPath + '/' + folderName);

                        vm.modalNewFolder.hide();

                        $('[name="new_folder_name"]').val("");

                        vm.folders.push(folderName);


                    } else {
                        console.log("Error:");
                        console.log(data);
                    }
                }
            );

        },
        upload: function (event: any): void {

            event.preventDefault();

            var vm = this;

            console.log("Uploading ...");

            var dirPath = vm.currentDirectory;

            var fileSelect = ($('#input-2') as any);
            var files = fileSelect[0].files;

            if (!files) {
                console.log("No file is selected");
                return;
            }

            var formData = new FormData();
            formData.append('_token', vm.csrfToken);
            formData.append('dir_path', dirPath);

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                formData.append('up_file[]', file, file.name);
            }


            $.ajax({
                url: event.target.action,
                type: 'POST',
                enctype: 'multipart/form-data',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data: any) {
                    if (typeof data.success !== undefined) {
                        console.log(data);

                        vm.modalUpload.hide();

                        fileSelect.val("");

                        //fileSelect.fileinput("clear");

                        for (var i = 0; i < data.uploadedFileNames.length; i++) {
                            console.log(vm.basename(data.uploadedFileNames[i]));
                            vm.files.push(vm.basename(data.uploadedFileNames[i]).concat('.').concat(vm.getFileExtension(data.uploadedFileNames[i])));
                        }
                    } else {
                        console.log("Error" + data);
                    }
                },
                error: function () {
                    console.log("Error in ajax form submission");
                }
            });
        },
        basename: function (url: string): string {
            //return ((/(([^\/\\\.#\? ]+)(\.\w+)*)([?#].+)?$/.exec(url)) != null) ? url[2] : '';
            return url.substring(url.lastIndexOf('/') + 1);
        },
        deleteModal: function (file: string): void {
            var vm = this;

            $('#content-name').text(vm.basename(file));

            $("#delete-submit").data('file', file);

            vm.modalDelete.show();

        },
        renameModal: function (file: string): void {
            var vm = this;

            vm.select(file);
            $("#selected").val(file);
            vm.modalRename.show();
        },
        renameFile: function (event: any): void {
            var vm = this;

            var file = vm.currentDirectory.concat('/').concat($('[name="old_name"]').val());
            console.log(file);

            this.http.put(event.target.action,{
                _token: vm.csrfToken,
                old_file: vm.currentDirectory.concat('/').concat($('[name="old_name"]').val()),
                new_file: vm.currentDirectory.concat('/').concat($('[name="new_name"]').val())
            }).pipe(
                catchError((error: any) => {
                    console.error(error);
                    return of(error);
                })
            ).subscribe((data: any) => {
                if (typeof data.success !== undefined) {
                    vm.open(vm.currentDirectory);
                    vm.modalRename.hide();
                    $('[name="new_name"]').val('');
                } else {
                    console.log(data);
                }
            });

        },
        shareLink(file: string): void {
            const baseUrl = window.location.origin;
            const path = `storage/${this.currentDirectory}/${file}`;
            const fullUrl = `${baseUrl}/${path}`;
        
            navigator.clipboard.writeText(fullUrl)
                .then(() => {
                    alert('Link copied to clipboard: '+ fullUrl);
                })
                .catch(err => {
                    console.error('Failed to copy:', err);
                });
        },
        deleteFile: function (): void {

            var vm = this;

            var deleteSubmit = $("#delete-submit");

            const file = vm.currentDirectory.concat('/').concat(deleteSubmit.data('file'));

            this.http.delete(environment.REST_API_BASE + '/file-manager/file?file=' + file)
            .pipe(
                retry(environment.API_RETRY),
                catchError((error: any) => {
                    console.error(error);
                    return of(error);
                })
            ).subscribe((data: any) => {

                ['files', 'folders'].forEach(key => {
                    vm[key] = vm[key].filter((item: string) => item !== deleteSubmit.data('file'));
                });

                vm.modalDelete.hide();

            });

        },
        getUrlVar: function (location: string, vary: any): any {
            var vars = [], hash;
            var hashes = location.slice(location.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[(hash[0] as any)] = hash[1];
            }
            return vars[vary];
        },
        getUrlParam: function (paramName: string): number {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);

            return Number((match && match.length > 1) ? match[1] : null);
        },
        returnFileUrl: function (filepath: string): void {

            try {
                // Simulate user action of selecting a file to be returned to CKEditor.
                var funcNum: number = this.getUrlParam("CKEditorFuncNum");
                var fileUrl: string = window.location.protocol + '//' +window.location.host+'/'+filepath;
                window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl, '');
                window.close();
            } catch (e) {
                console.log(window.opener.CKEDITOR)
                console.log(e);
            }
        },
        getFileExtension: function (fileName: string): string {
            return fileName.substring(fileName.lastIndexOf('.') + 1);
        },
        isKnownExtension: function (fileName: string): boolean {
            var vm = this;

            return vm.knownFileExtensions.includes(vm.getFileExtension(fileName).toLowerCase());
        }
    }

});
