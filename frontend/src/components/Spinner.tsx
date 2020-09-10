import * as React from 'react';
import {Fade, LinearProgress, MuiThemeProvider, Theme} from "@material-ui/core";
import {useContext} from "react";
import LoadingContext from "./loading/LoadingContext";

function makeLocalTheme(theme: Theme): Theme {
    return {
        ...theme,
        palette: {
            ...theme.palette,
            primary: theme.palette.error,
            type: 'dark'
        }
    }
}


const Spinner = () => {
    const loading = useContext(LoadingContext);
    return (
        <MuiThemeProvider theme={makeLocalTheme}>
            <Fade in={loading}>
                <LinearProgress
                    color={'primary'}
                    style={{
                        position: 'fixed',
                        width: '100%',
                        zIndex: 9999
                    }}
                />
            </Fade>
        </MuiThemeProvider>
    );
};

export default Spinner;
