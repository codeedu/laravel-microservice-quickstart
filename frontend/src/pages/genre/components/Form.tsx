import React, {useEffect, useState} from "react";
import {Box, Button, ButtonProps, MenuItem, TextField} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../../util/http/category-http";
import genreHttp from "../../../util/http/genre-http";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

interface Category {
    id: string,
    name: string
}

const Form = () => {
    const classes = useStyles();
    const [categories,setCategories] = useState<Category[]>([]);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "contained",
        color: "secondary"
    }

    const {register, handleSubmit, getValues, setValue, watch} = useForm({
        defaultValues: {
            categories_id: []
        }

    });

    useEffect(() => {
        register({name: 'categories_id'})
    },[register])

    useEffect(() => {
        categoryHttp
            .list()
            .then(({data}) => setCategories(data.data))
    },[]);

    function onSubmit(formData,event){
       genreHttp
            .create(formData)
            .then((response) => console.log(response))
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name={"name"}
                label={"Nome"}
                fullWidth
                variant={"outlined"}
                inputRef={register}
            />
            <TextField
                select
                name={"categories_id"}
                value={watch('categories_id')}
                label={"Categorias"}
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                onChange={(e) => {
                    setValue('categories_id', e.target.value)
                }}
                SelectProps={{
                    multiple: true
                }}
            >
                <MenuItem value="">
                    <em>Selecione uma categoria</em>
                </MenuItem>
                {
                    categories.map(
                        (category,key) => (
                            <MenuItem key={key} value={category.id}>{category.name}</MenuItem>
                        )
                    )
                }
            </TextField>
            <Box dir={'rtl'}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(),null)}>Salvar</Button>
                <Button {...buttonProps} type={'submit'}>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};

export default Form;