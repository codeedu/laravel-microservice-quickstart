import axios, {AxiosError, AxiosRequestConfig, AxiosResponse} from "axios";
import { keycloak } from "../auth";

export const httpVideo = axios.create({
    baseURL: process.env.REACT_APP_MICRO_VIDEO_API_URL
});

const instances = [httpVideo];
httpVideo.interceptors.request.use(authInterceptor);

function authInterceptor(request: AxiosRequestConfig): AxiosRequestConfig | Promise<AxiosRequestConfig>{
    if(keycloak?.token){
        addToken(request);
        return request;
    }
    return new Promise((resolve, reject) => {
        keycloak.onAuthSuccess = () => {
            addToken(request);
            resolve(request);
        }
        keycloak.onAuthError = () => {
            reject(request);
        }
    })
}

function addToken(request: AxiosRequestConfig){
    request.headers["Authorization"] = `Bearer ${keycloak.token}`
}

export function addGlobalRequestInterceptor(
    onFulfilled?: (value: AxiosRequestConfig) => AxiosRequestConfig | Promise<AxiosRequestConfig>,
    onRejected?: (error: any) => any
) {
    const ids: number[] = [];
    for (let i of instances) {
        const id = i.interceptors.request.use(onFulfilled, onRejected);
        ids.push(id);
    }
    return ids;
}

export function removeGlobalRequestInterceptor(ids: number[]) {
    ids.forEach(
        (id, index) => instances[index].interceptors.request.eject(id)
    )
}

export function addGlobalResponseInterceptor(
    onFulfilled?: (value: AxiosResponse) => AxiosResponse | Promise<AxiosResponse>,
    onRejected?: (error: AxiosError) => any
) {
    const ids: number[] = [];
    for (let i of instances) {
        const id = i.interceptors.response.use(onFulfilled, onRejected);
        ids.push(id);
    }
    return ids;
}

export function removeGlobalResponseInterceptor(ids: number[]) {
    ids.forEach(
        (id, index) => instances[index].interceptors.response.eject(id)
    )
}

//interceptors global
//interceptors instancias

