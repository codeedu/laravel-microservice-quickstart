import * as React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import {Link} from "react-router-dom";
import AddIcon from '@material-ui/icons/Add';
import Table from "./Table";

const PageList = () => {
    return (
        <Page title={'Listagem categorias'}>
            <Box dir={'rtl'} paddingBottom={2}>
                <Fab
                    title="Adicionar categoria"
                    color={'secondary'}
                    size="small"
                    component={Link}
                    to="/categories/create"
                >
                    <AddIcon/>
                </Fab>
            </Box>
            <Box>
                <Table/>
            </Box>
        </Page>
    );
};


export default PageList;
