import React from 'react';
import {Page} from "../../components/Page";
import Form from "./components/Form";
import {useParams} from 'react-router';

type Category = {
    id: string
};

const CategoryForm = () => {
    const {id} = useParams<Category>();
    return (
        <Page title={!id ? 'Criar categorias' : 'Editar Categoria'}>
            <Form/>
        </Page>
    );
};

export default CategoryForm;