/**
 * Kind of an entry of the file manager:
 * - "dir"   : a folder
 * - "image" : a file the UI shows as an image thumbnail
 * - "text"  : a file the UI is able to show as read only text
 * - "other" : everything else (only a download is offered)
 */
export type FileKind = "dir" | "image" | "text" | "other";

/**
 * A listing entry: a "dirs" or "files" name of the API plus everything the UI
 * derives from it.
 */
export interface FileManagerEntry {
    name: string;
    type: "dir" | "file";
    extension: string;
    kind: FileKind;
}

/**
 * Payload of "GET /file-manager" (the disk listing of a directory).
 */
export interface FileManagerResponse {
    old_path?: string;
    current_dir: string;
    dirs: string[];
    files: string[];
    allowed_extensions?: {
        image?: string[];
        text?: string[];
    };
    mode?: string | null;
}

/**
 * UI state of the preview panel. "content" holds the text of a text file (the
 * image preview is rendered from the file URL), "message" explains why there
 * is nothing to show.
 */
export interface FilePreviewState {
    name: string;
    extension: string;
    kind: FileKind;
    loading: boolean;
    content: string | null;
    message: string | null;
}
