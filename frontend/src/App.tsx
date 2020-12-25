import React from 'react';
import {Navbar} from "./components/Navbar";
import {Box, CssBaseline, MuiThemeProvider} from "@material-ui/core";
import {BrowserRouter} from 'react-router-dom';
import AppRouter from "./routes/AppRouter";
import Breadcrumbs from "./components/Breadcrumbs";
import theme from "./theme";
import SnackbarProvider from "./components/SnackbarProvider";
const App: React.FC = () => {
    return (
        <MuiThemeProvider theme={theme}>
            <SnackbarProvider>
                <CssBaseline/>
                <BrowserRouter>
                    <Navbar/>
                    <Box paddingTop={'70px'}>
                        <Breadcrumbs/>
                        <AppRouter/>
                    </Box>
                </BrowserRouter>
             </SnackbarProvider>
        </MuiThemeProvider>
        );
};

export default App