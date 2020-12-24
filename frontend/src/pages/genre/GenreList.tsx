import React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import {Link} from "react-router-dom";
import AddIcon from "@material-ui/icons/Add";
import Table from "./components/Table";


type Props = {
    
};
const GenreList = (props: Props) => {
    return (
        <Page title='Listagem de Gêneros'>
            <Box dir={'rtl'} paddingBottom={2}>
                <Fab
                    title={'Adicionar Gêneros'}
                    size={'small'}
                    color={'secondary'}
                    component={Link}
                    to={'/genres/create'}
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

export default GenreList;