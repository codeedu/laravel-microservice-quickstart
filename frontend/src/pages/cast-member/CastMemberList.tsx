import React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import {Link} from "react-router-dom";
import AddIcon from "@material-ui/icons/Add";
import Table from "./components/Table";



const CastMemberList = () => {
    return (
        <Page title='Listagem de membors de elencos'>
            <Box dir={'rtl'} paddingBottom={2}>
                <Fab
                    title={'Adicionar membro de elenco'}
                    size={'small'}
                    color={'secondary'}
                    component={Link}
                    to={'/cast-members/create'}
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

export default CastMemberList