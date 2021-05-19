// @flow
import * as React from 'react';
import LoadingContext from "./LoadingContext";
import {useEffect, useMemo, useState} from "react";
import {
    addGlobalRequestInterceptor,
    addGlobalResponseInterceptor,
    removeGlobalRequestInterceptor, removeGlobalResponseInterceptor
} from "../../util/http";

export const LoadingProvider = (props) => {
    const [loading, setLoading] = useState<boolean>(false);
    const [countRequest, setCountRequest] = useState(0);

    //useMemo vs useCallback
    useMemo(() => {
        let isSubscribed = true;
        //axios.interceptors.request.use();
        const requestIds = addGlobalRequestInterceptor((config) => {
            if (isSubscribed && (!config.headers || !config.headers.hasOwnProperty('x-ignore-loading'))) {
                setLoading(true);
                setCountRequest((prevCountRequest) => prevCountRequest + 1);
            }
            return config;
        });
        //axios.interceptors.response.use();
        const responseIds = addGlobalResponseInterceptor(
            (response) => {
                if (isSubscribed && !response.config.headers.hasOwnProperty('x-ignore-loading')) {
                    decrementCountRequest();
                }
                return response;
            },
            (error) => {
                if (isSubscribed && (!error.config || !error.config.headers.hasOwnProperty('x-ignore-loading'))) {
                    decrementCountRequest();
                }
                return Promise.reject(error);
            }
        );
        return () => {
            isSubscribed = false;
            removeGlobalRequestInterceptor(requestIds);
            removeGlobalResponseInterceptor(responseIds);
        }
    }, [true]);

    useEffect(() => {
        if (!countRequest) {
            setLoading(false);
        }
    }, [countRequest]);

    function decrementCountRequest() {
        setCountRequest((prevCountRequest) => prevCountRequest - 1);
    }

    return (
        <LoadingContext.Provider value={loading}>
            {props.children}
        </LoadingContext.Provider>
    );
};
