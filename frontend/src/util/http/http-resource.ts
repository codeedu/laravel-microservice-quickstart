import {AxiosInstance, AxiosRequestConfig, AxiosResponse} from "axios";

export default class HttpResource {

    constructor(protected http: AxiosInstance, protected resource) {

    }

    list<T = any>(): Promise<AxiosResponse<T>> {
        const config:AxiosRequestConfig = {
            params: {
                all: ''
            }
        };
        return this.http.get<T>(this.resource, config);
    }

    get<T = any>(id): Promise<AxiosResponse<T>> {
        return this.http.get<T>(`${this.resource}/${id}`);
    }

    create<T = any>(data): Promise<AxiosResponse<T>> {
        return this.http.post<T>(this.resource, data);
    }

    update<T = any>(id, data): Promise<AxiosResponse<T>> {
        return this.http.put<T>(`${this.resource}/${id}`, data);
    }

    delete<T = any>(id): Promise<AxiosResponse<T>> {
        return this.http.delete<T>(`${this.resource}/${id}`);
    }
}
