import {all} from 'redux-saga/effects';
import {uploadWatcherSaga} from "./upload/sagas";

export default function* rootSaga() {
    yield all([
        uploadWatcherSaga()
    ]);
}
