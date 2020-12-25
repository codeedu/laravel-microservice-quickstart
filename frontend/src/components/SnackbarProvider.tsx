import React from 'react';
import {SnackbarProviderProps, SnackbarProvider as NotistackProvider} from "notistack";
import {IconButton} from "@material-ui/core";
import {Close as CloseIcon} from "@material-ui/icons";



const SnackbarProvider: React.FC<SnackbarProviderProps> = (props) => {
    let snackbarProviderRef;
    const defaultProps: SnackbarProviderProps = {
        children: undefined,
        autoHideDuration: 3000,
        maxSnack: 3,
        anchorOrigin:{
            horizontal: 'right',
            vertical: 'top'
        },
        ref: (el) => snackbarProviderRef = el,
        action: (key => (
            <IconButton
                color={'inherit'}
                style={{fontSize: 20}}
                onClick={() => snackbarProviderRef.closeSnackbar(key)}
            >
                <CloseIcon/>
            </IconButton>
        ))
    }

    const newProps = {...defaultProps, ...props}

    return (
        <NotistackProvider {...newProps}>
            {props.children}
        </NotistackProvider>

    );
};

export default SnackbarProvider;