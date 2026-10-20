/// <reference types="bootstrap-fileinput" />
import { defineComponent } from "vue";
import $ from "jquery";
import DeleteModal from "../delete-modal/DeleteModal.vue";
import { environment } from "../../environments/environment";
import {
  FileKind,
  FileManagerEntry,
  FileManagerResponse,
  FilePreviewState,
} from "../../model/FileManagerResponse";
import { catchError, map, of, retry } from "rxjs";

/**
 * "allowed_extensions" of the API is the authoritative image list of the
 * backend; these are the remaining formats a browser renders inline, so they
 * get a thumbnail (and an inline preview) as well.
 */
const EXTRA_IMAGE_EXTENSIONS: string[] = ["svg", "ico", "bmp", "avif"];

/**
 * Used until the API announces the extensions of the current disk.
 */
const DEFAULT_IMAGE_EXTENSIONS: string[] = [
  "jpg",
  "png",
  "jpeg",
  "webp",
  "gif",
];

/**
 * The text preview reads the public URL of the file client side, so anything
 * bigger is offered as a download instead of being loaded into the panel.
 */
export const MAX_PREVIEW_BYTES = 256 * 1024;

/**
 * bootstrap-fileinput understands a couple of options the bundled typings do
 * not know about.
 */
type FileManagerFileInputOptions = BootstrapFileInput.FileInputOptions & {
  dropZoneClickTitle?: string;
  msgPlaceholder?: string;
};

export default defineComponent({
  name: "FileManager",
  components: {
    DeleteModal,
  },
  inject: ["bootstrap"],
  props: {
    directory: {
      type: String,
      default: "",
    },
    disks: {
      type: Array<string>,
      default: [],
    },
    mode: {
      type: String,
      default: "standalone",
    },
  },
  data: function () {
    return {
      /** Image extensions announced by the API for the current disk. */
      allowedImageExtensions: [] as string[],
      /** Extensions the UI is able to show as plain text. */
      textFileExtensions: [
        "txt",
        "log",
        "md",
        "markdown",
        "rst",
        "json",
        "json5",
        "js",
        "mjs",
        "cjs",
        "ts",
        "tsx",
        "jsx",
        "vue",
        "css",
        "scss",
        "sass",
        "less",
        "map",
        "html",
        "htm",
        "xhtml",
        "xml",
        "yml",
        "yaml",
        "ini",
        "conf",
        "env",
        "csv",
        "tsv",
        "sql",
        "sh",
        "bash",
        "bat",
        "twig",
        "blade",
        "php",
        "toml",
      ],
      currentDirectory: this.directory,
      currentDisk: "local",
      folders: [] as string[],
      files: [] as string[],
      filter: null as string | null,
      view: "grid" as "grid" | "list",
      sortAsc: true,
      selected: null as string | null,
      selectedForDelete: null as string | null,
      preview: null as FilePreviewState | null,
      createType: "folder" as "folder" | "file",
      createName: "",
      createError: null as string | null,
      renameName: "",
      renameError: null as string | null,
      uploadError: null as string | null,
      uploadProgress: 0,
      uploading: false,
      dragging: false,
      loading: false,
      flash: null as { type: string; text: string } | null,
      flashTimer: null as number | null,
      openError: null as any,
    };
  },
  mounted: function () {
    const vm = this;

    console.log("VueJS: FileManager started");
    vm.open(vm.currentDirectory, false);
    vm.initFileInput();

    document.addEventListener("keydown", vm.onKeydown);
  },
  beforeDestroy: function () {
    document.removeEventListener("keydown", this.onKeydown);

    if (this.flashTimer !== null) {
      clearTimeout(this.flashTimer);
    }
  },
  computed: {
    breadcrumb: function (): { text: string; link: string }[] {
      const vm = this;

      const here = vm.currentDirectory
        .split("/")
        .filter((part: string) => part !== "");

      return here.map((part: string, index: number) => {
        return { text: part, link: here.slice(0, index + 1).join("/") };
      });
    },
    /**
     * The CMS renders its breadcrumbs through "app/View/breadcrumb.blade.php",
     * which a component can not include (the path is kept on the client), so
     * the file manager breadcrumb uses the very same markup and arrow divider
     * as that partial.
     */
    breadcrumbDividerStyle: function (): Record<string, string> {
      return {
        "--bs-breadcrumb-divider":
          "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E\")",
      };
    },
    /**
     * Every extension the UI treats as an image.
     */
    imageExtensions: function (): string[] {
      const vm = this;
      const allowed = vm.allowedImageExtensions;

      return (allowed.length > 0 ? allowed : DEFAULT_IMAGE_EXTENSIONS).concat(
        EXTRA_IMAGE_EXTENSIONS,
      );
    },
    folderEntries: function (): FileManagerEntry[] {
      const vm = this;

      return vm.folders.map((name: string) => vm.entry(name, "dir"));
    },
    fileEntries: function (): FileManagerEntry[] {
      const vm = this;

      return vm.files.map((name: string) => vm.entry(name, "file"));
    },
    filteredFolders: function (): FileManagerEntry[] {
      const vm = this;

      return vm.applySort(vm.applyFilter(vm.folderEntries));
    },
    filteredFiles: function (): FileManagerEntry[] {
      const vm = this;

      return vm.applySort(vm.applyFilter(vm.fileEntries));
    },
    visibleEntries: function (): FileManagerEntry[] {
      const vm = this;

      return vm.filteredFolders.concat(vm.filteredFiles);
    },
    itemCount: function (): number {
      const vm = this;

      return vm.filteredFolders.length + vm.filteredFiles.length;
    },
    totalCount: function (): number {
      const vm = this;

      return vm.folders.length + vm.files.length;
    },
    selectedEntry: function (): FileManagerEntry | null {
      const vm = this;

      if (vm.selected === null) {
        return null;
      }

      return (
        vm.visibleEntries.find(
          (entry: FileManagerEntry) => entry.name === vm.selected,
        ) || null
      );
    },
    tileClass: function (): string[] {
      const vm = this;

      return [
        "col-6",
        "col-sm-4",
        vm.preview === null ? "col-md-2" : "col-md-3",
      ];
    },
  },
  methods: {
    translate: function (key: string): string {
      return (this as any).$t(key);
    },
    joinPath: function (directory: string, name: string): string {
      return directory === "" ? name : directory + "/" + name;
    },
    /**
     * Public URL of an entry. The listing works on disk paths, but the browser
     * reaches the files through "storage/", so the segments are encoded.
     */
    storageUrl: function (name: string): string {
      const path = this.joinPath(this.currentDirectory, name);

      return (
        "storage/" +
        path
          .split("/")
          .map((part: string) => encodeURIComponent(part))
          .join("/")
      );
    },
    basename: function (path: string): string {
      return path.substring(path.lastIndexOf("/") + 1);
    },
    getFileExtension: function (fileName: string): string {
      const base = this.basename(fileName);

      return base.indexOf(".") === -1
        ? ""
        : base.substring(base.lastIndexOf(".") + 1).toLowerCase();
    },
    kindOf: function (extension: string): FileKind {
      const vm = this;
      const ext = (extension || "").toLowerCase();

      if (ext === "") {
        return "other";
      }

      if (vm.imageExtensions.includes(ext)) {
        return "image";
      }

      if (vm.textFileExtensions.includes(ext)) {
        return "text";
      }

      return "other";
    },
    entry: function (name: string, type: "dir" | "file"): FileManagerEntry {
      const vm = this;
      const extension = type === "dir" ? "" : vm.getFileExtension(name);

      return {
        name: name,
        type: type,
        extension: extension,
        kind: type === "dir" ? "dir" : vm.kindOf(extension),
      };
    },
    applyFilter: function (entries: FileManagerEntry[]): FileManagerEntry[] {
      const filter = (this.filter || "").trim().toLowerCase();

      if (filter === "") {
        return entries;
      }

      return entries.filter((entry: FileManagerEntry) =>
        entry.name.toLowerCase().includes(filter),
      );
    },
    applySort: function (entries: FileManagerEntry[]): FileManagerEntry[] {
      const direction = this.sortAsc ? 1 : -1;

      return entries
        .slice()
        .sort(
          (a: FileManagerEntry, b: FileManagerEntry) =>
            a.name.localeCompare(b.name) * direction,
        );
    },
    toggleSort: function (): void {
      this.sortAsc = !this.sortAsc;
    },
    sortIcon: function (): string {
      return this.sortAsc ? "fa-sort-up" : "fa-sort-down";
    },
    setView: function (view: "grid" | "list"): void {
      this.view = view;
    },
    modal: function (ref: string): any {
      const vm = this;
      const target = vm.$refs[ref] as any;
      const element = target && target.$el ? target.$el : target;

      return vm.bootstrap.Modal.getOrCreateInstance(element);
    },
    showModal: function (ref: string): void {
      this.modal(ref).show();
    },
    hideModal: function (ref: string): void {
      this.modal(ref).hide();
    },
    /**
     * Focus (and select) the input of a modal once it is completely visible.
     */
    focusModalInput: function (modalRef: string, inputRef: string): void {
      const vm = this;
      const element = vm.modal(modalRef)._element as HTMLElement;

      element.addEventListener(
        "shown.bs.modal",
        () => {
          const input = vm.$refs[inputRef] as HTMLInputElement | undefined;

          if (input) {
            input.focus();
            input.select();
          }
        },
        { once: true },
      );
    },
    flashMessage: function (type: string, text: string | null): void {
      const vm = this;

      if (!text) {
        return;
      }

      vm.flash = { type: type, text: text };

      if (vm.flashTimer !== null) {
        clearTimeout(vm.flashTimer);
      }

      vm.flashTimer = setTimeout(() => {
        vm.flash = null;
      }, 4000);
    },
    /**
     * Error text of a failed request: AxiosError, AxiosResponse or a plain API
     * payload are all accepted.
     */
    errorMessage: function (response: any, fallback?: string): string {
      const data = response?.response?.data ?? response?.data ?? response;

      return (
        data?.error ||
        data?.danger ||
        data?.warning ||
        data?.message ||
        fallback ||
        this.translate("file.action_failed")
      );
    },
    open: function (
      folder: string,
      useCurrent = true,
      done?: () => void,
    ): void {
      const vm = this;

      const folderToOpen = useCurrent
        ? vm.joinPath(vm.currentDirectory, folder)
        : folder;

      vm.openError = null;
      vm.loading = true;

      vm.http
        .get(
          environment.REST_API_BASE +
            "/file-manager?disk=" +
            encodeURIComponent(vm.currentDisk) +
            "&path=" +
            encodeURIComponent(folderToOpen),
        )
        .pipe(
          retry(environment.API_RETRY),
          map((response: any) => response.data as FileManagerResponse),
          catchError((error: any) => {
            console.error(error);
            vm.openError = error.response?.data || { error: "Unknown error" };
            vm.loading = false;

            return of(null);
          }),
        )
        .subscribe((data: FileManagerResponse | null) => {
          if (data === null) {
            return;
          }

          vm.loading = false;
          vm.currentDirectory = data.current_dir ?? "";
          vm.folders = data.dirs ?? [];
          vm.files = data.files ?? [];
          vm.selected = null;
          vm.closePreview();

          if (data.allowed_extensions && data.allowed_extensions.image) {
            vm.allowedImageExtensions = data.allowed_extensions.image;
          }

          console.log("FileManager opened: " + vm.currentDirectory);

          if (done) {
            done();
          }
        });
    },
    refresh: function (): void {
      this.open(this.currentDirectory, false);
    },
    switchDisk: function (disk: string): void {
      const vm = this;

      vm.currentDisk = disk;
      vm.currentDirectory = "";
      vm.open("", false);
    },
    up: function (): void {
      const vm = this;

      const parts = vm.currentDirectory
        .split("/")
        .filter((part: string) => part !== "");

      parts.pop();

      vm.open(parts.join("/"), false);
    },
    /**
     * Single click: select the entry, and for files load the preview (or hand
     * the URL back to CKEditor in embed mode).
     */
    selectEntry: function (entry: FileManagerEntry): void {
      const vm = this;

      vm.selected = entry.name;

      if (entry.type !== "file") {
        return;
      }

      if (vm.mode === "embed") {
        vm.returnFileUrl(vm.storageUrl(entry.name));

        return;
      }

      vm.loadPreview(entry.name);
    },
    /**
     * Double click: open a folder, preview a file.
     */
    openEntry: function (entry: FileManagerEntry): void {
      const vm = this;

      if (entry.type === "dir") {
        vm.open(entry.name);

        return;
      }

      if (vm.mode === "embed") {
        vm.returnFileUrl(vm.storageUrl(entry.name));

        return;
      }

      vm.loadPreview(entry.name);
    },
    /**
     * Opens the preview panel of a file. Reading a text file is asynchronous,
     * so the pending promise is returned to be able to wait for it.
     */
    loadPreview: function (name: string): void | Promise<void> {
      const vm = this;

      vm.selected = name;

      if (vm.preview !== null && vm.preview.name === name) {
        return;
      }

      const entry = vm.entry(name, "file");

      vm.preview = {
        name: name,
        extension: entry.extension,
        kind: entry.kind,
        loading: entry.kind === "text",
        content: null,
        message: entry.kind === "other" ? vm.translate("file.no_preview") : null,
      };

      // An image is rendered straight from its URL, a text file is read client
      // side and anything else only offers a download.
      if (entry.kind === "text") {
        return vm.loadTextPreview(name);
      }
    },
    /**
     * The API has no endpoint for the content of a file, so the text preview
     * reads its public URL directly. Anything unexpected (missing file, binary
     * content, huge file) degrades into a message.
     */
    loadTextPreview: function (name: string): Promise<void> {
      const vm = this;

      return vm.requestText(vm.storageUrl(name)).then(
        (response: Response) => {
          const preview = vm.preview;

          if (preview === null || preview.name !== name) {
            return;
          }

          if (!response.ok) {
            preview.loading = false;
            preview.message = vm.translate("file.preview_failed");

            return;
          }

          if (
            Number(response.headers.get("content-length") || 0) >
            MAX_PREVIEW_BYTES
          ) {
            preview.loading = false;
            preview.message = vm.translate("file.preview_too_large");

            return;
          }

          return response.text().then((text: string) => {
            const current = vm.preview;

            if (current === null || current.name !== name) {
              return;
            }

            current.loading = false;

            if (text.indexOf("\u0000") !== -1) {
              current.message = vm.translate("file.preview_binary");

              return;
            }

            current.content = text;
          });
        },
        () => {
          const preview = vm.preview;

          if (preview !== null && preview.name === name) {
            preview.loading = false;
            preview.message = vm.translate("file.preview_failed");
          }
        },
      );
    },
    /**
     * Kept apart so the way the content is fetched can be replaced (and
     * stubbed by the tests).
     */
    requestText: function (url: string): Promise<Response> {
      return fetch(url);
    },
    closePreview: function (): void {
      this.preview = null;
    },
    copyLink: function (name: string): void {
      const vm = this;
      const url = window.location.origin + "/" + vm.storageUrl(name);

      if (!navigator.clipboard) {
        vm.flashMessage("warning", vm.translate("file.copy_failed"));

        return;
      }

      navigator.clipboard
        .writeText(url)
        .then(() => vm.flashMessage("success", vm.translate("file.link_copied")))
        .catch((error: any) => {
          console.error("Failed to copy:", error);
          vm.flashMessage("danger", vm.translate("file.copy_failed"));
        });
    },
    openCreate: function (type: "folder" | "file"): void {
      const vm = this;

      vm.createType = type;
      vm.createName = "";
      vm.createError = null;
      vm.focusModalInput("createModal", "createInput");
      vm.showModal("createModal");
    },
    createNew: function (): void {
      const vm = this;
      const type = vm.createType;
      const name = (vm.createName || "").trim();

      vm.createError = null;

      if (name === "") {
        vm.createError = vm.translate("file.action_failed");

        return;
      }

      vm.http
        .put(
          environment.REST_API_BASE +
            "/file-manager/new-" +
            type +
            "?disk=" +
            encodeURIComponent(vm.currentDisk),
          {
            _token: vm.csrfToken,
            dir_path: vm.currentDirectory,
            new_folder_name: name,
            new_file_name: name,
          },
        )
        .pipe(
          catchError((error: any) => {
            console.error(error);
            vm.createError = vm.errorMessage(error);

            return of(null);
          }),
        )
        .subscribe((response: any) => {
          if (response === null) {
            return;
          }

          const data = response.data;

          if (!data?.success) {
            vm.createError = vm.errorMessage(response);

            return;
          }

          vm.hideModal("createModal");
          vm.createName = "";
          vm.flashMessage("success", data.success);

          vm.open(vm.currentDirectory, false, () => {
            if (type === "file") {
              vm.loadPreview(name);
            }
          });
        });
    },
    openRename: function (name: string): void {
      const vm = this;

      vm.selected = name;
      vm.renameName = name;
      vm.renameError = null;
      vm.focusModalInput("renameModal", "renameInput");
      vm.showModal("renameModal");
    },
    renameFile: function (): void {
      const vm = this;
      const name = vm.selected;
      const newName = (vm.renameName || "").trim();

      vm.renameError = null;

      if (name === null || newName === "") {
        vm.renameError = vm.translate("file.action_failed");

        return;
      }

      vm.http
        .put(
          environment.REST_API_BASE +
            "/file-manager/rename?disk=" +
            encodeURIComponent(vm.currentDisk),
          {
            _token: vm.csrfToken,
            old_file: vm.joinPath(vm.currentDirectory, name),
            new_file: vm.joinPath(vm.currentDirectory, newName),
          },
        )
        .pipe(
          catchError((error: any) => {
            console.error(error);
            vm.renameError = vm.errorMessage(error);

            return of(null);
          }),
        )
        .subscribe((response: any) => {
          if (response === null) {
            return;
          }

          const data = response.data;

          if (!data?.success) {
            vm.renameError = vm.errorMessage(response);

            return;
          }

          vm.hideModal("renameModal");
          vm.renameName = "";
          vm.flashMessage("success", data.success);

          if (vm.preview !== null && vm.preview.name === name) {
            vm.closePreview();
          }

          vm.open(vm.currentDirectory, false, () => {
            vm.selected = newName;
          });
        });
    },
    openUpload: function (): void {
      const vm = this;

      vm.uploadError = null;
      vm.uploadProgress = 0;
      vm.showModal("uploadModal");
    },
    onFileSelect: function (): void {
      this.uploadError = null;
    },
    /**
     * The upload input of the dialog is a bootstrap-fileinput (a jQuery
     * plugin, bundled by app.ts): it renders the drop zone, the thumbnail
     * preview and the "select" button. The upload itself is still done by the
     * form, so the plugin's own upload button stays switched off.
     */
    initFileInput: function (): void {
      const input = this.$refs.uploadInput as HTMLInputElement | undefined;

      // Without the plugin (or without jQuery) the plain file input is left
      // untouched, it can still be submitted.
      if (!input || !$.fn?.fileinput) {
        return;
      }

      const options: FileManagerFileInputOptions = {
        showUpload: false,
        showCaption: true,
        showPreview: true,
        showBrowse: true,
        showRemove: true,
        browseLabel: this.translate("actions.select"),
        removeLabel: this.translate("actions.remove"),
        removeTitle: this.translate("actions.remove"),
        msgPlaceholder: this.translate("actions.select"),
        dropZoneTitle: this.translate("file.drop_files"),
        dropZoneClickTitle: "<br>",
      };

      $(input).fileinput(options);
    },
    /**
     * The plugin keeps its own preview and caption, so the selection is also
     * cleared through it (its "reset" does not touch the native input).
     */
    resetUploadInput: function (): void {
      const input = this.$refs.uploadInput as HTMLInputElement | undefined;

      if (!input) {
        return;
      }

      input.value = "";

      if (!$.fn?.fileinput) {
        return;
      }

      $(input).fileinput("reset");
    },
    uploadSelected: function (): void {
      const vm = this;
      const input = vm.$refs.uploadInput as HTMLInputElement | undefined;

      vm.uploadFiles(input?.files ?? null);
    },
    uploadFiles: function (files: FileList | null): void {
      const vm = this;

      if (!files || files.length === 0) {
        vm.uploadError = vm.translate("file.action_failed");

        return;
      }

      const formData = new FormData();

      formData.append("dir_path", vm.currentDirectory);

      for (let i = 0; i < files.length; i++) {
        formData.append("up_file[]", files[i], files[i].name);
      }

      vm.uploading = true;
      vm.uploadError = null;
      vm.uploadProgress = 0;

      vm.http
        .post(
          environment.REST_API_BASE +
            "/file-manager?disk=" +
            encodeURIComponent(vm.currentDisk),
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (event: ProgressEvent) => {
              if (event.total) {
                vm.uploadProgress = Math.round(
                  (event.loaded / event.total) * 100,
                );
              }
            },
          },
        )
        .pipe(
          retry(environment.API_RETRY),
          catchError((error: any) => {
            console.error(error);
            vm.uploadError = vm.errorMessage(error);

            return of(null);
          }),
        )
        .subscribe((response: any) => {
          vm.uploading = false;
          vm.uploadProgress = 0;

          if (response === null) {
            return;
          }

          const data = response.data;

          if (!data?.success) {
            vm.uploadError = vm.errorMessage(response);

            return;
          }

          vm.hideModal("uploadModal");
          vm.resetUploadInput();
          vm.flashMessage("success", data.success);
          vm.open(vm.currentDirectory, false);
        });
    },
    onDrop: function (event: DragEvent): void {
      const vm = this;

      vm.dragging = false;

      const files = event.dataTransfer?.files ?? null;

      if (files && files.length > 0) {
        vm.uploadFiles(files);
      }
    },
    onDragOver: function (): void {
      this.dragging = true;
    },
    /**
     * Moving over a child of the drop zone also triggers "dragleave" of the
     * zone itself; only leaving it completely should hide the overlay.
     */
    onDragLeave: function (event?: DragEvent): void {
      const zone = event ? (event.currentTarget as Node | null) : null;
      const entering = event ? (event.relatedTarget as Node | null) : null;

      if (zone && entering && zone.contains(entering)) {
        return;
      }

      this.dragging = false;
    },
    deleteModal: function (name: string): void {
      const vm = this;

      vm.selectedForDelete = name;
      vm.showModal("deletemodal");
    },
    deleteFile: function (): void {
      const vm = this;
      const name = vm.selectedForDelete;

      if (name === null) {
        return;
      }

      vm.http
        .delete(
          environment.REST_API_BASE +
            "/file-manager/file?disk=" +
            encodeURIComponent(vm.currentDisk) +
            "&file=" +
            encodeURIComponent(vm.joinPath(vm.currentDirectory, name)),
        )
        .pipe(
          retry(environment.API_RETRY),
          catchError((error: any) => {
            console.error(error);
            vm.flashMessage("danger", vm.errorMessage(error));

            return of(null);
          }),
        )
        .subscribe((response: any) => {
          if (response !== null) {
            const data = response.data;

            if (data?.success) {
              vm.flashMessage("success", data.success);
            } else {
              vm.flashMessage("warning", vm.errorMessage(response));
            }

            ["folders", "files"].forEach((key) => {
              vm[key] = vm[key].filter((item: string) => item !== name);
            });
          }

          if (vm.preview !== null && vm.preview.name === name) {
            vm.closePreview();
          }

          if (vm.selected === name) {
            vm.selected = null;
          }

          vm.selectedForDelete = null;
          vm.hideModal("deletemodal");
        });
    },
    isVisible: function (): boolean {
      const element = this.$el as HTMLElement | undefined;

      return (
        !!element &&
        (element.offsetParent !== null || element.getClientRects().length > 0)
      );
    },
    /**
     * Keyboard shortcuts of the listing: Delete, F2 (rename), Enter (open) and
     * Escape (close the preview). Disabled while typing or while a modal is up.
     */
    onKeydown: function (event: KeyboardEvent): void {
      const vm = this;
      const target = event.target as HTMLElement | null;
      const tag = (target?.tagName || "").toLowerCase();

      if (
        target?.isContentEditable ||
        tag === "input" ||
        tag === "textarea" ||
        tag === "select"
      ) {
        return;
      }

      if (event.ctrlKey || event.metaKey || event.altKey) {
        return;
      }

      if (document.querySelector(".modal.show") || !vm.isVisible()) {
        return;
      }

      if (event.key === "Delete" && vm.selected !== null) {
        event.preventDefault();
        vm.deleteModal(vm.selected);

        return;
      }

      if (event.key === "F2" && vm.selected !== null) {
        event.preventDefault();
        vm.openRename(vm.selected);

        return;
      }

      if (event.key === "Enter" && vm.selectedEntry !== null) {
        event.preventDefault();
        vm.openEntry(vm.selectedEntry);

        return;
      }

      if (event.key === "Escape" && vm.preview !== null) {
        event.preventDefault();
        vm.closePreview();
      }
    },
    getUrlParam: function (paramName: string): number {
      const reParam = new RegExp("(?:[?&]|&)" + paramName + "=([^&]+)", "i");
      const match = window.location.search.match(reParam);

      return Number(match && match.length > 1 ? match[1] : null);
    },
    returnFileUrl: function (filepath: string): void {
      try {
        // Simulate user action of selecting a file to be returned to CKEditor.
        const funcNum: number = this.getUrlParam("CKEditorFuncNum");
        const fileUrl: string =
          window.location.protocol +
          "//" +
          window.location.host +
          "/" +
          filepath;
        window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl, "");
        window.close();
      } catch (e) {
        console.log(window.opener.CKEDITOR);
        console.log(e);
      }
    },
  },
});
