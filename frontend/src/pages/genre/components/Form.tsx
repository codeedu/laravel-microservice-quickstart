import React, {useEffect, useState} from "react";
import {Box, Button, ButtonProps, MenuItem, TextField} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../../util/http/category-http";
import genreHttp from "../../../util/http/genre-http";
import {useHistory, useParams} from "react-router";
import {useSnackbar} from "notistack";
import * as yup from "../../../util/vendor/yup";
import { yupResolver } from '@hookform/resolvers/yup';
import castMemberHttp from "../../../util/http/cast-member-http";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

interface Genre {
    id: string,
    name: string,
    categories_id: string[]
}

const SchemaValidation = yup.object({
    name: yup.string()
        .label('Nome')
        .required()
        .max(255),
    categories_id: yup.array()
        .label('Categorias')
        .required()
}).defined();


const Form = () => {
    const classes = useStyles();
    const history = useHistory();
    const {id} = useParams<{id: string}>();
    const snackbar = useSnackbar();

    const [categories,setCategories] = useState<Genre[]>([]);
    const [genre,setGenre] = useState<Genre | null>(null);
    const [loading, setLoading] = useState<boolean>(false)

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "contained",
        color: "secondary"
    }

    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        watch,
        errors,
        reset
    } = useForm<Genre>({
        resolver: yupResolver(SchemaValidation),
        defaultValues: {
            categories_id: []
        }
    });

    useEffect(() => {
        register({name: 'categories_id'})
    },[register])

    useEffect(() => {
       let isSubscribed = true;
        (async () =>{
            setLoading(true);
            const promise = [categoryHttp.list()];
            if(id){
                promise.push(genreHttp.get(id))
            }
            try{
                if(isSubscribed){
                    const [categoriesResponse, genteResponse] = await Promise.all(promise);
                    setCategories(categoriesResponse.data.data);
                    if(id){
                        setGenre(genteResponse.data.data);
                        reset({
                            ...genteResponse.data.data,
                            categories_id: genteResponse.data.data.categories.map(category => category.id)
                        });
                    }
                }
            }catch(error){
                console.error(error);
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    {variant: 'error'}
                )
            }finally {
                setLoading(false);
            }
        })();
        return() => {
            isSubscribed = false;
        }
    },[]);

    async function onSubmit(formData,event){
        setLoading(true);
        try{
            const http = !genre
                ?genreHttp.create(formData)
                :genreHttp.update(genre.id, formData)
            const {data} = await http;
            snackbar.enqueueSnackbar(
                'Gênero salvo com Sucesso',
                {variant: 'success'}
            )
            setTimeout(() => {
                event ? (
                        id
                            ? history.replace(`/genres/${data.data.id}/edit`)
                            : history.push(`/genres/${data.data.id}/edit`)
                    )
                    :history.push('/genres')
            })
        }catch(error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                'Não é possível salvar o gênero',
                {variant: 'error'}
            )
        }finally{
            setLoading(false)
        }
    }
    console.log(errors)
    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name={"name"}
                label={"Nome"}
                fullWidth
                variant={"outlined"}
                inputRef={register}
                disabled={loading}
                error={errors.name !== undefined}
                helperText={errors.name?.message}
                InputLabelProps={{
                    shrink: true
                }}

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
                    setValue('categories_id', e.target.value,{ shouldValidate: true })
                }}
                SelectProps={{
                    multiple: true
                }}
                disabled={loading}
                error={errors.categories_id !== undefined}
               // helperText={errors.categories_id && errors.categories_id.message}
                InputLabelProps={{
                    shrink: true
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