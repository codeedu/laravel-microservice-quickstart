import React from 'react';
import {Page} from "../../components/Page";
import Form from "./components/Form";

type Props = {
    
};
const GenreForm = (props: Props) => {
    return (
        <Page title={'Criar Elenco'}>
            <Form/>
        </Page>
    );
};

export default GenreForm;