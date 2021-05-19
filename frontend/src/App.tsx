import React from 'react';
import './App.css';
import {Navbar} from "./components/Navbar";
import {Box, MuiThemeProvider, CssBaseline} from "@material-ui/core";
import {BrowserRouter} from "react-router-dom";
import AppRouter from "./routes/AppRouter";
import Breadcrumbs from "./components/Breadcrumbs";
import theme from "./theme";
import {SnackbarProvider} from "./components/SnackbarProvider";
import Spinner from "./components/Spinner";
import {LoadingProvider} from "./components/loading/LoadingProvider";
import { ReactKeycloakProvider } from '@react-keycloak/web';
import { keycloak, keycloakConfig } from './util/auth';

const App: React.FC = () => {
    return (
        <ReactKeycloakProvider authClient={keycloak} initOptions={keycloakConfig}>
            <LoadingProvider>
                <MuiThemeProvider theme={theme}>
                    <SnackbarProvider>
                        <CssBaseline/>
                        <BrowserRouter basename={process.env.REACT_APP_BASENAME}>
                            <Spinner/>
                            <Navbar/>
                            <Box paddingTop={'70px'}>
                                <Breadcrumbs/>
                                <AppRouter/>
                            </Box>
                        </BrowserRouter>
                    </SnackbarProvider>
                </MuiThemeProvider>
            </LoadingProvider>
        </ReactKeycloakProvider>
    );
}

export default App;
