import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios';
import { Observable } from 'rxjs';

function toObservable<T>(promise: Promise<AxiosResponse<T>>): Observable<AxiosResponse<T>> {
    return new Observable<AxiosResponse<T>>((subscriber) => {
        promise
            .then((response) => {
                subscriber.next(response);
                subscriber.complete();
            })
            .catch((error) => {
                subscriber.error(error);
            });
    });
}

class Http {
    private instance: AxiosInstance;

    constructor() {
        this.instance = axios.create();
    }

    get defaults() {
        return this.instance.defaults;
    }

    get interceptors() {
        return this.instance.interceptors;
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    get<T = any>(url: string, config?: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.get(url, config));
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    post<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.post(url, data, config));
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    put<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.put(url, data, config));
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    patch<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.patch(url, data, config));
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    delete<T = any>(url: string, config?: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.delete(url, config));
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    head<T = any>(url: string, config?: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.head(url, config));
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    request<T = any>(config: AxiosRequestConfig): Observable<AxiosResponse<T>> {
        return toObservable<T>(this.instance.request(config));
    }
}

export const http = new Http();
export default http;
