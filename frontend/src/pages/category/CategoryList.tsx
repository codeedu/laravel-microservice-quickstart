import React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import {Link} from "react-router-dom";
import AddIcon from "@material-ui/icons/Add";
import Table from "./components/Table";

export const CategoryList = () => {
    return (
        <Page title='Listagem de Categorias'>
            <Box dir={'rtl'} paddingBottom={2}>
                <Fab
                    title={'Adicionar Categoria'}
                    size={'small'}
                    color={'secondary'}
                    component={Link}
                    to={'/categorias/create'}
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

export default CategoryList;