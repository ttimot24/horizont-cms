import { describe, it, expect, vi, beforeEach } from "vitest";
import { of, throwError } from "rxjs";

/**
 * The component imports DeleteModal.vue, which vitest (node environment, no
 * Vue plugin) can not transform, so it is replaced by a stub.
 */
vi.mock("../delete-modal/DeleteModal.vue", () => ({
    default: { name: "DeleteModal" },
}));

/**
 * The upload input is a bootstrap-fileinput (a jQuery plugin); jQuery itself
 * needs a DOM, so the plugin calls are recorded here.
 */
const fileinputCalls = vi.hoisted(() => ({
    init: vi.fn(),
    method: vi.fn(),
}));

vi.mock("jquery", () => {
    const jq = Object.assign(
        () => ({
            fileinput: (option: unknown) =>
                (typeof option === "string" ? fileinputCalls.method(option) : fileinputCalls.init(option)),
        }),
        { fn: { fileinput: vi.fn() } },
    );

    return { default: jq };
});

/* eslint-disable @typescript-eslint/no-explicit-any -- the internal state of
   the component is exercised through plain objects, which are typed loosely. */

import FileManager, { MAX_PREVIEW_BYTES } from "./FileManager";

const component: any = FileManager;

const data = (): any => component.data.call({ directory: "" });

/**
 * The component logic is tested without mounting it: every method is copied on
 * a plain object (and the computed properties are exposed as getters), so the
 * methods can call each other through "this".
 */
const fakeVm = (overrides: any = {}): any => {
    const vm: any = Object.assign({}, component.methods, data(), {
        $t: (key: string) => key,
        $refs: {},
        requestText: vi.fn(() => Promise.resolve(fakeResponse())),
        bootstrap: {
            Modal: {
                getOrCreateInstance: vi.fn(() => ({ show: vi.fn(), hide: vi.fn() })),
            },
        },
    }, overrides);

    Object.keys(component.computed).forEach((key: string) => {
        Object.defineProperty(vm, key, {
            get: () => component.computed[key].call(vm),
            configurable: true,
        });
    });

    return vm;
};

const blob = (name: string): any => Object.assign(new Blob(["content"]), { name: name });

const fileEntry = (overrides: any = {}): any =>
    Object.assign({
        name: "file.txt",
        type: "file",
        extension: "txt",
        kind: "text",
    }, overrides);

/**
 * Minimal stand in of a fetch Response: only what the preview uses.
 */
const fakeResponse = (options: any = {}): any => {
    const headers: Record<string, string> = Object.assign(
        { "content-length": "5" },
        options.headers || {},
    );

    return {
        ok: options.ok !== false,
        headers: {
            get: (key: string) => headers[key.toLowerCase()] ?? null,
        },
        text: () => Promise.resolve(options.body ?? "hello"),
    };
};

const keyEvent = (key: string): any => ({
    key: key,
    target: { tagName: "DIV" },
    preventDefault: vi.fn(),
});

describe("FileManager - Unit Tests", () => {

    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe("Component Definition", () => {

        it("should have correct component name", () => {
            expect(component.name).toBe("FileManager");
        });

        it("should register the delete modal", () => {
            expect(component.components).toHaveProperty("DeleteModal");
        });

        it("should inject the bootstrap provider", () => {
            expect(component.inject).toContain("bootstrap");
        });

        it("should define its props", () => {
            expect(Object.keys(component.props)).toEqual(["directory", "disks", "mode"]);
            expect(component.props.directory.type).toBe(String);
            expect(component.props.disks.type).toBe(Array);
            expect(component.props.mode.type).toBe(String);
        });

        it("should default to standalone mode and the root directory", () => {
            expect(component.props.mode.default).toBe("standalone");
            expect(component.props.directory.default).toBe("");
        });
    });

    describe("Data", () => {

        it("should expose sane defaults", () => {
            const state = data();

            expect(state.currentDirectory).toBe("");
            expect(state.currentDisk).toBe("local");
            expect(state.view).toBe("grid");
            expect(state.sortAsc).toBe(true);
            expect(state.filter).toBeNull();
            expect(state.selected).toBeNull();
            expect(state.selectedForDelete).toBeNull();
            expect(state.preview).toBeNull();
            expect(state.folders).toEqual([]);
            expect(state.files).toEqual([]);
            expect(state.allowedImageExtensions).toEqual([]);
        });

        it("should know which extensions it can render", () => {
            const state = data();

            expect(state.textFileExtensions).toContain("md");
            expect(state.textFileExtensions).toContain("php");
        });
    });

    describe("Path and URL helpers", () => {

        it("joinPath should not add a leading slash", () => {
            const vm = fakeVm();

            expect(vm.joinPath("", "a.txt")).toBe("a.txt");
            expect(vm.joinPath("images", "a.txt")).toBe("images/a.txt");
        });

        it("basename should return the last segment", () => {
            const vm = fakeVm();

            expect(vm.basename("storage/images/a.txt")).toBe("a.txt");
            expect(vm.basename("a.txt")).toBe("a.txt");
        });

        it("getFileExtension should be lower case and empty when there is none", () => {
            const vm = fakeVm();

            expect(vm.getFileExtension("a.TXT")).toBe("txt");
            expect(vm.getFileExtension("archive.tar.gz")).toBe("gz");
            expect(vm.getFileExtension("README")).toBe("");
            expect(vm.getFileExtension("storage/images/a.png")).toBe("png");
        });

        it("storageUrl should encode every segment and keep the storage prefix", () => {
            const vm = fakeVm({ currentDirectory: "images/header images" });

            expect(vm.storageUrl("a b.png")).toBe("storage/images/header%20images/a%20b.png");
        });

        it("storageUrl should work on the disk root", () => {
            const vm = fakeVm();

            expect(vm.storageUrl("logo.png")).toBe("storage/logo.png");
        });
    });

    describe("Kinds", () => {

        it("kindOf should classify image, text and other files", () => {
            const vm = fakeVm();

            expect(vm.kindOf("jpg")).toBe("image");
            expect(vm.kindOf("PNG")).toBe("image");
            expect(vm.kindOf("svg")).toBe("image");
            expect(vm.kindOf("md")).toBe("text");
            expect(vm.kindOf("zip")).toBe("other");
            expect(vm.kindOf("")).toBe("other");
        });

        it("should trust the extensions announced by the API", () => {
            const vm = fakeVm({ allowedImageExtensions: ["avif"] });

            expect(vm.kindOf("avif")).toBe("image");
            expect(vm.kindOf("jpg")).toBe("other");
        });

        it("should fall back to the known image extensions", () => {
            const vm = fakeVm();

            expect(vm.imageExtensions).toContain("jpg");
            expect(vm.imageExtensions).toContain("gif");
        });
    });

    describe("Entries", () => {

        it("entry should derive the kind of a file", () => {
            const vm = fakeVm();

            expect(vm.entry("a.png", "file")).toEqual({
                name: "a.png",
                type: "file",
                extension: "png",
                kind: "image",
            });
        });

        it("entry should describe folders", () => {
            const vm = fakeVm();

            expect(vm.entry("pages", "dir")).toEqual({
                name: "pages",
                type: "dir",
                extension: "",
                kind: "dir",
            });
        });

        it("fileEntries and folderEntries should map the listing", () => {
            const vm = fakeVm({ folders: ["pages"], files: ["a.txt", "b.png"] });

            expect(component.computed.folderEntries.call(vm).map((e: any) => e.kind)).toEqual(["dir"]);
            expect(component.computed.fileEntries.call(vm).map((e: any) => e.name)).toEqual(["a.txt", "b.png"]);
        });
    });

    describe("Filtering and sorting", () => {

        it("applyFilter should be case insensitive and empty safe", () => {
            const vm = fakeVm({ filter: "RE" });
            const entries = [fileEntry({ name: "readme.md" }), fileEntry({ name: "logo.png" })];

            expect(vm.applyFilter(entries).map((e: any) => e.name)).toEqual(["readme.md"]);

            vm.filter = null;
            expect(vm.applyFilter(entries).length).toBe(2);

            vm.filter = "   ";
            expect(vm.applyFilter(entries).length).toBe(2);
        });

        it("applySort should sort by name and respect the direction", () => {
            const vm = fakeVm();
            const entries = [fileEntry({ name: "b.txt" }), fileEntry({ name: "a.txt" })];

            expect(vm.applySort(entries).map((e: any) => e.name)).toEqual(["a.txt", "b.txt"]);

            vm.sortAsc = false;
            expect(vm.applySort(entries).map((e: any) => e.name)).toEqual(["b.txt", "a.txt"]);
        });

        it("applySort should not mutate the input", () => {
            const vm = fakeVm();
            const entries = [fileEntry({ name: "b.txt" }), fileEntry({ name: "a.txt" })];

            vm.applySort(entries);

            expect(entries.map((e: any) => e.name)).toEqual(["b.txt", "a.txt"]);
        });

        it("toggleSort should flip the direction", () => {
            const vm = fakeVm();

            vm.toggleSort();
            expect(vm.sortAsc).toBe(false);

            vm.toggleSort();
            expect(vm.sortAsc).toBe(true);
        });

        it("sortIcon should reflect the current direction", () => {
            const vm = fakeVm();

            expect(vm.sortIcon()).toBe("fa-sort-up");

            vm.sortAsc = false;
            expect(vm.sortIcon()).toBe("fa-sort-down");
        });

        it("visibleEntries should list folders first", () => {
            const vm = fakeVm({ folders: ["zzz"], files: ["aaa.txt"] });

            expect(component.computed.visibleEntries.call(vm).map((e: any) => e.name)).toEqual(["zzz", "aaa.txt"]);
        });

        it("itemCount and totalCount should follow the filter", () => {
            const vm = fakeVm({ folders: ["zzz"], files: ["a.txt", "b.png"], filter: "a" });

            expect(component.computed.itemCount.call(vm)).toBe(1);
            expect(component.computed.totalCount.call(vm)).toBe(3);
        });

        it("tileClass should leave room for the preview panel", () => {
            const vm = fakeVm();

            expect(vm.tileClass).toContain("col-md-2");

            vm.preview = { name: "a.png" };
            expect(vm.tileClass).toContain("col-md-3");
        });
    });

    describe("Breadcrumb", () => {

        it("should build the links of the current directory", () => {
            const vm = fakeVm({ currentDirectory: "images/pages" });

            expect(component.computed.breadcrumb.call(vm)).toEqual([
                { text: "images", link: "images" },
                { text: "pages", link: "images/pages" },
            ]);
        });

        it("should be empty on the disk root", () => {
            const vm = fakeVm();

            expect(component.computed.breadcrumb.call(vm)).toEqual([]);
        });

        it("should carry the arrow divider of the CMS breadcrumb partial", () => {
            const vm = fakeVm();

            expect(vm.breadcrumbDividerStyle["--bs-breadcrumb-divider"]).toContain(
                "data:image/svg+xml",
            );
        });
    });

    describe("Navigation", () => {

        it("up should open the parent directory", () => {
            const vm = fakeVm({ currentDirectory: "images/pages" });
            vm.open = vi.fn();

            vm.up();

            expect(vm.open).toHaveBeenCalledWith("images", false);
        });

        it("up should do nothing but stay valid on the root", () => {
            const vm = fakeVm();
            vm.open = vi.fn();

            vm.up();

            expect(vm.open).toHaveBeenCalledWith("", false);
        });

        it("switchDisk should reset the directory", () => {
            const vm = fakeVm({ currentDirectory: "images" });
            vm.open = vi.fn();

            vm.switchDisk("public");

            expect(vm.currentDisk).toBe("public");
            expect(vm.currentDirectory).toBe("");
            expect(vm.open).toHaveBeenCalledWith("", false);
        });

        it("refresh should reload the current directory", () => {
            const vm = fakeVm({ currentDirectory: "images" });
            vm.open = vi.fn();

            vm.refresh();

            expect(vm.open).toHaveBeenCalledWith("images", false);
        });
    });

    describe("Selection", () => {

        it("selectEntry on a folder should only select it", () => {
            const vm = fakeVm();
            vm.loadPreview = vi.fn();
            vm.returnFileUrl = vi.fn();

            vm.selectEntry(fileEntry({ name: "pages", type: "dir", kind: "dir" }));

            expect(vm.selected).toBe("pages");
            expect(vm.loadPreview).not.toHaveBeenCalled();
            expect(vm.returnFileUrl).not.toHaveBeenCalled();
        });

        it("selectEntry on a file should select and preview it", () => {
            const vm = fakeVm();
            vm.loadPreview = vi.fn();

            vm.selectEntry(fileEntry());

            expect(vm.selected).toBe("file.txt");
            expect(vm.loadPreview).toHaveBeenCalledWith("file.txt");
        });

        it("selectEntry on a file should hand the URL back in embed mode", () => {
            const vm = fakeVm({ mode: "embed" });
            vm.loadPreview = vi.fn();
            vm.returnFileUrl = vi.fn();

            vm.selectEntry(fileEntry());

            expect(vm.returnFileUrl).toHaveBeenCalledTimes(1);
            expect(vm.loadPreview).not.toHaveBeenCalled();
        });

        it("openEntry on a folder should open it", () => {
            const vm = fakeVm();
            vm.open = vi.fn();

            vm.openEntry(fileEntry({ name: "pages", type: "dir", kind: "dir" }));

            expect(vm.open).toHaveBeenCalledWith("pages");
        });
    });

    describe("Preview", () => {

        it("should render an image from its URL without reading it", () => {
            const vm = fakeVm();
            vm.requestText = vi.fn();

            vm.loadPreview("a.png");

            expect(vm.requestText).not.toHaveBeenCalled();
            expect(vm.preview.name).toBe("a.png");
            expect(vm.preview.kind).toBe("image");
            expect(vm.preview.loading).toBe(false);
            expect(vm.preview.message).toBeNull();
        });

        it("should report that a file can not be previewed", () => {
            const vm = fakeVm();
            vm.requestText = vi.fn();

            vm.loadPreview("a.zip");

            expect(vm.requestText).not.toHaveBeenCalled();
            expect(vm.preview.kind).toBe("other");
            expect(vm.preview.message).toBe("file.no_preview");
        });

        it("should read a text file from its public URL", async () => {
            const vm = fakeVm();

            const pending = vm.loadPreview("a.txt");

            expect(vm.preview.kind).toBe("text");
            expect(vm.preview.loading).toBe(true);
            expect(vm.requestText).toHaveBeenCalledWith("storage/a.txt");

            await pending;

            expect(vm.preview.loading).toBe(false);
            expect(vm.preview.content).toBe("hello");
            expect(vm.preview.message).toBeNull();
        });

        it("should refuse to load a huge text file", async () => {
            const vm = fakeVm({
                requestText: vi.fn(() => Promise.resolve(fakeResponse({
                    headers: { "content-length": String(MAX_PREVIEW_BYTES + 1) },
                }))),
            });

            await vm.loadPreview("a.txt");

            expect(vm.preview.content).toBeNull();
            expect(vm.preview.loading).toBe(false);
            expect(vm.preview.message).toBe("file.preview_too_large");
        });

        it("should still load a file up to the preview limit", async () => {
            const vm = fakeVm({
                requestText: vi.fn(() => Promise.resolve(fakeResponse({
                    headers: { "content-length": String(MAX_PREVIEW_BYTES) },
                }))),
            });

            await vm.loadPreview("a.txt");

            expect(vm.preview.content).toBe("hello");
            expect(vm.preview.message).toBeNull();
        });

        it("should refuse to render binary content as text", async () => {
            const vm = fakeVm({
                requestText: vi.fn(() => Promise.resolve(fakeResponse({ body: "a\u0000b" }))),
            });

            await vm.loadPreview("a.txt");

            expect(vm.preview.content).toBeNull();
            expect(vm.preview.message).toBe("file.preview_binary");
        });

        it("should report a missing file", async () => {
            const vm = fakeVm({
                requestText: vi.fn(() => Promise.resolve(fakeResponse({ ok: false }))),
            });

            await vm.loadPreview("a.txt");

            expect(vm.preview.content).toBeNull();
            expect(vm.preview.loading).toBe(false);
            expect(vm.preview.message).toBe("file.preview_failed");
        });

        it("should report a failed request", async () => {
            const vm = fakeVm({
                requestText: vi.fn(() => Promise.reject(new Error("offline"))),
            });

            await vm.loadPreview("a.txt");

            expect(vm.preview.content).toBeNull();
            expect(vm.preview.loading).toBe(false);
            expect(vm.preview.message).toBe("file.preview_failed");
        });

        it("should drop the content of a file that is not selected anymore", async () => {
            let resolve: any = null;
            const response = new Promise((done) => {
                resolve = done;
            });

            const vm = fakeVm({ requestText: vi.fn(() => response) });

            const pending = vm.loadPreview("a.txt");

            vm.closePreview();
            resolve(fakeResponse());

            await pending;

            expect(vm.preview).toBeNull();
        });

        it("should not read the same file twice", () => {
            const vm = fakeVm();

            vm.loadPreview("a.txt");
            vm.loadPreview("a.txt");

            expect(vm.requestText).toHaveBeenCalledTimes(1);
        });

        it("closePreview should clear the panel", () => {
            const vm = fakeVm({ preview: { name: "a.txt" } });

            vm.closePreview();

            expect(vm.preview).toBeNull();
        });
    });

    describe("Delete", () => {

        it("should do nothing without a selected entry", () => {
            const vm = fakeVm();
            vm.http = { delete: vi.fn() };

            vm.deleteFile();

            expect(vm.http.delete).not.toHaveBeenCalled();
        });

        it("deleteModal should remember the entry and open the modal", () => {
            const vm = fakeVm();
            vm.showModal = vi.fn();

            vm.deleteModal("a.txt");

            expect(vm.selectedForDelete).toBe("a.txt");
            expect(vm.showModal).toHaveBeenCalledWith("deletemodal");
        });

        it("should remove a deleted file from the listing", () => {
            const vm = fakeVm({
                selectedForDelete: "a.txt",
                selected: "a.txt",
                files: ["a.txt", "b.txt"],
                folders: [],
            });
            vm.http = { delete: vi.fn(() => of({ data: { success: "Deleted successfully!" } })) };

            vm.deleteFile();

            expect(vm.files).toEqual(["b.txt"]);
            expect(vm.selected).toBeNull();
            expect(vm.selectedForDelete).toBeNull();
            expect(vm.flash).toEqual({ type: "success", text: "Deleted successfully!" });
        });

        it("should keep the entry and warn when the deletion fails", () => {
            const vm = fakeVm({
                selectedForDelete: "a.txt",
                files: ["a.txt"],
            });
            vm.http = {
                delete: vi.fn(() => throwError(() => ({ response: { data: { error: "Nope" } } }))),
            };

            vm.deleteFile();

            expect(vm.files).toEqual(["a.txt"]);
            expect(vm.flash).toEqual({ type: "danger", text: "Nope" });
        });
    });

    describe("Opening a directory", () => {

        it("should load the listing", () => {
            const vm = fakeVm();
            const done = vi.fn();
            vm.http = {
                get: vi.fn(() => of({
                    data: {
                        old_path: "images/",
                        current_dir: "images",
                        dirs: ["sub"],
                        files: ["a.txt"],
                        allowed_extensions: { image: ["png"] },
                    },
                })),
            };

            vm.open("images", false, done);

            expect(vm.currentDirectory).toBe("images");
            expect(vm.folders).toEqual(["sub"]);
            expect(vm.files).toEqual(["a.txt"]);
            expect(vm.allowedImageExtensions).toEqual(["png"]);
            expect(vm.loading).toBe(false);
            expect(done).toHaveBeenCalledTimes(1);
        });

        it("should keep the known image extensions without the API field", () => {
            const vm = fakeVm({ allowedImageExtensions: ["png"] });
            vm.http = {
                get: vi.fn(() => of({ data: { current_dir: "", dirs: [], files: [] } })),
            };

            vm.open("", false);

            expect(vm.allowedImageExtensions).toEqual(["png"]);
        });

        it("should report a failed listing without keeping the spinner", () => {
            const vm = fakeVm();
            vm.http = {
                get: vi.fn(() => throwError(() => ({ response: { data: { error: "Nope" } } }))),
            };

            vm.open("", false);

            expect(vm.loading).toBe(false);
            expect(vm.openError).toEqual({ error: "Nope" });
        });
    });

    describe("Upload", () => {

        it("should reject an empty selection", () => {
            const vm = fakeVm();
            vm.http = { post: vi.fn() };

            vm.uploadFiles(null);

            expect(vm.http.post).not.toHaveBeenCalled();
            expect(vm.uploadError).toBe("file.action_failed");
        });

        it("should post every file of the selection", () => {
            const vm = fakeVm({ currentDirectory: "images" });
            vm.http = { post: vi.fn(() => of({ data: { success: "Files uploaded successfully!" } })) };
            vm.open = vi.fn();

            vm.uploadFiles([blob("a.png"), blob("b.png")] as any);

            expect(vm.http.post).toHaveBeenCalledTimes(1);
            expect(vm.uploading).toBe(false);
        });

        it("onDrop should forward the dropped files", () => {
            const vm = fakeVm();
            vm.uploadFiles = vi.fn();

            vm.onDrop({ dataTransfer: { files: [{ name: "a.png" }] } } as any);

            expect(vm.dragging).toBe(false);
            expect(vm.uploadFiles).toHaveBeenCalledTimes(1);
        });

        it("drag helpers should toggle the overlay", () => {
            const vm = fakeVm();

            vm.onDragOver();
            expect(vm.dragging).toBe(true);

            vm.onDragLeave();
            expect(vm.dragging).toBe(false);
        });

        it("should keep the overlay while the cursor is inside the drop zone", () => {
            const vm = fakeVm({ dragging: true });
            const zone = { contains: (node: any) => node !== null };

            vm.onDragLeave({ currentTarget: zone, relatedTarget: {} } as any);

            expect(vm.dragging).toBe(true);

            vm.onDragLeave({ currentTarget: zone, relatedTarget: null } as any);
            expect(vm.dragging).toBe(false);
        });
    });

    describe("Upload input (bootstrap-fileinput)", () => {

        it("should turn the input into a fileinput on mount", () => {
            const vm = fakeVm();
            const input = {} as any;

            vm.$refs = { uploadInput: input };

            vm.initFileInput();

            expect(fileinputCalls.init).toHaveBeenCalledTimes(1);
            expect(fileinputCalls.init).toHaveBeenCalledWith({
                showUpload: false,
                showCaption: true,
                showPreview: true,
                showBrowse: true,
                showRemove: true,
                browseLabel: "actions.select",
                removeLabel: "actions.remove",
                removeTitle: "actions.remove",
                msgPlaceholder: "actions.select",
                dropZoneTitle: "file.drop_files",
                dropZoneClickTitle: "<br>",
            });
        });

        it("should leave a plain input alone without the input ref", () => {
            const vm = fakeVm();

            vm.$refs = {};

            vm.initFileInput();

            expect(fileinputCalls.init).not.toHaveBeenCalled();
        });

        it("should clear the native value and the preview on reset", () => {
            const vm = fakeVm();
            const input = { value: "selected" } as any;

            vm.$refs = { uploadInput: input };

            vm.resetUploadInput();

            expect(input.value).toBe("");
            expect(fileinputCalls.method).toHaveBeenCalledWith("reset");
        });

        it("should keep a missing input harmless on reset", () => {
            const vm = fakeVm();

            vm.$refs = {};

            expect(() => vm.resetUploadInput()).not.toThrow();
            expect(fileinputCalls.method).not.toHaveBeenCalled();
        });
    });

    describe("Flash messages", () => {

        it("should ignore empty messages", () => {
            const vm = fakeVm();

            vm.flashMessage("success", null);

            expect(vm.flash).toBeNull();
        });

        it("should expose the message and its type", () => {
            const vm = fakeVm();

            vm.flashMessage("danger", "Nope");

            expect(vm.flash).toEqual({ type: "danger", text: "Nope" });
        });
    });

    describe("Error messages", () => {

        it("should understand API payloads, responses and axios errors", () => {
            const vm = fakeVm();

            expect(vm.errorMessage({ data: { danger: "Nope" } })).toBe("Nope");
            expect(vm.errorMessage({ data: { warning: "Nope" } })).toBe("Nope");
            expect(vm.errorMessage({ response: { data: { error: "Nope" } } })).toBe("Nope");
            expect(vm.errorMessage({})).toBe("file.action_failed");
            expect(vm.errorMessage({}, "file.preview_failed")).toBe("file.preview_failed");
        });
    });

    describe("Keyboard shortcuts", () => {

        it("should ignore keys while typing", () => {
            const vm = fakeVm({ $el: {}, selected: "a.txt" });
            vm.deleteModal = vi.fn();
            vi.stubGlobal("document", { querySelector: () => null });

            vm.onKeydown(Object.assign(keyEvent("Delete"), { target: { tagName: "INPUT" } }));

            expect(vm.deleteModal).not.toHaveBeenCalled();

            vi.unstubAllGlobals();
        });

        it("should open the delete modal with Delete", () => {
            const vm = fakeVm({ $el: {}, selected: "a.txt" });
            vm.deleteModal = vi.fn();
            vi.stubGlobal("document", { querySelector: () => null });

            vm.onKeydown(keyEvent("Delete"));

            expect(vm.deleteModal).toHaveBeenCalledWith("a.txt");

            vi.unstubAllGlobals();
        });

        it("should rename with F2 and close the preview with Escape", () => {
            const vm = fakeVm({ $el: {}, selected: "a.txt", preview: { name: "a.txt" } });
            vm.openRename = vi.fn();
            vm.closePreview = vi.fn();
            vi.stubGlobal("document", { querySelector: () => null });

            vm.onKeydown(keyEvent("F2"));
            vm.onKeydown(keyEvent("Escape"));

            expect(vm.openRename).toHaveBeenCalledWith("a.txt");
            expect(vm.closePreview).toHaveBeenCalledTimes(1);

            vi.unstubAllGlobals();
        });

        it("should do nothing while a modal is open", () => {
            const vm = fakeVm({ $el: {}, selected: "a.txt" });
            vm.deleteModal = vi.fn();
            vi.stubGlobal("document", { querySelector: () => ({}) });

            vm.onKeydown(keyEvent("Delete"));

            expect(vm.deleteModal).not.toHaveBeenCalled();

            vi.unstubAllGlobals();
        });
    });
});
