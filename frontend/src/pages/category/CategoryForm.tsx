import React from 'react';
import {Page} from "../../components/Page";
import Form from "./components/Form";

type Props = {
    
};
const CategoryForm = (props: Props) => {
    return (
        <Page title={'Cria categoria'}>
            <Form/>
        </Page>
    );
};

export default CategoryForm;