import * as Typings from "./types";
import {createActions, createReducer} from 'reduxsauce';
import update from 'immutability-helper';

export const {Types, Creators} = createActions<{
    ADD_UPLOAD: string,
    REMOVE_UPLOAD: string,
    UPDATE_PROGRESS: string,
    SET_UPLOAD_ERROR: string
}, {
    addUpload(payload: Typings.AddUploadAction['payload']): Typings.AddUploadAction
    removeUpload(payload: Typings.RemoveUploadAction['payload']): Typings.RemoveUploadAction
    updateProgress(payload: Typings.UpdateProgressAction['payload']): Typings.UpdateProgressAction
    setUploadError(payload: Typings.SetUploadErrorAction['payload']): Typings.SetUploadErrorAction
}>
({
    addUpload: ['payload'],
    removeUpload: ['payload'],
    updateProgress: ['payload'],
    setUploadError: ['payload'],
});

export const INITIAL_STATE: Typings.UploadState = {
    uploads: []
};

const reducer = createReducer<Typings.UploadState, Typings.Actions>(INITIAL_STATE, {
    [Types.ADD_UPLOAD]: addUpload as any,
    [Types.REMOVE_UPLOAD]: removeUpload as any,
    [Types.UPDATE_PROGRESS]: updateProgress as any,
    [Types.SET_UPLOAD_ERROR]: setUploadError as any
});

export default reducer;

function addUpload(state = INITIAL_STATE, action: Typings.AddUploadAction): Typings.UploadState {
    if (!action.payload.files.length) {
        return state;
    }

    const index = findIndexUpload(state, action.payload.video.id);

    if (index !== -1 && state.uploads[index].progress < 1) {
        return state;
    }

    const uploads = index === -1
        ? state.uploads
        : update(state.uploads, {
            $splice: [[index, 1]]
        });

    return {
        uploads: [
            ...uploads,
            {
                video: action.payload.video,
                progress: 0,
                files: action.payload.files.map(file => ({
                    fileField: file.fileField,
                    filename: file.file.name,
                    progress: 0
                }))
            }
        ]
    };
}

function removeUpload(state: Typings.UploadState = INITIAL_STATE, action: Typings.RemoveUploadAction): Typings.UploadState {
    const uploads = state.uploads.filter(upload => upload.video.id !== action.payload.id);

    if (uploads.length === state.uploads.length) {
        return state;
    }

    return {
        uploads
    }
}

function updateProgress(state: Typings.UploadState = INITIAL_STATE, action: Typings.UpdateProgressAction): Typings.UploadState {
    //indexUpload, indexFile
    const videoId = action.payload.video.id;
    const fileField = action.payload.fileField;
    const {indexUpload, indexFile} = findIndexUploadAndFile(state, videoId, fileField)

    if (typeof indexUpload === "undefined") {
        return state;
    }

    const upload = state.uploads[indexUpload];
    const file = upload.files[indexFile];

    if (file.progress === action.payload.progress) {
        return state;
    }

    const uploads = update(state.uploads, {
        [indexUpload]: {
            $apply(upload) {
                const files = update(upload.files, {
                    [indexFile]: {
                        $set: {...file, progress: action.payload.progress}
                    }
                });
                const progress = calculateGlobalProgress(files);
                console.log({...upload, progress, files});
                return {...upload, progress, files}
            }
        }
    });

    return {uploads};
}

function setUploadError(state: Typings.UploadState = INITIAL_STATE, action: Typings.SetUploadErrorAction): Typings.UploadState {
    //indexUpload, indexFile
    const videoId = action.payload.video.id;
    const fileField = action.payload.fileField;
    const {indexUpload, indexFile} = findIndexUploadAndFile(state, videoId, fileField);

    if (typeof indexUpload === "undefined") {
        return state;
    }

    const upload = state.uploads[indexUpload];
    const file = upload.files[indexFile];

    const uploads = update(state.uploads, {
        [indexUpload]: {
            files: {
                [indexFile]: {
                    $set: {...file, error: action.payload.error, progress: 1}
                }
            }
        }
    });

    return {uploads};
}

function findIndexUploadAndFile(state: Typings.UploadState, videoId, fileField): { indexUpload?, indexFile? } {
    const indexUpload = findIndexUpload(state, videoId);
    if (indexUpload === -1) {
        return {};
    }

    const upload = state.uploads[indexUpload];
    const indexFile = findIndexFile(upload.files, fileField);
    return indexFile === -1 ? {} : {indexUpload, indexFile}

}

function calculateGlobalProgress(files: Array<{ progress }>) {
    const countFiles = files.length;
    if (!countFiles) {
        return 0;
    }
    const sumProgress = files.reduce((sum, file) => sum + file.progress, 0)
    return sumProgress / countFiles;
}

function findIndexUpload(state: Typings.UploadState, id: string) {
    return state.uploads.findIndex((upload) => upload.video.id === id);
}

function findIndexFile(files: Array<{ fileField }>, fileField: string) {
    return files.findIndex(file => file.fileField === fileField);
}

//adicionar arquivos
//video vento levou
