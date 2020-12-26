import React, {useEffect, useState} from 'react';
import {Checkbox, FormControlLabel, TextField} from "@material-ui/core";
import {useForm} from "react-hook-form";
import categoryHttp from "../../../util/http/category-http";
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from '../../../util/vendor/yup'
import {useParams,useHistory} from "react-router";
import {useSnackbar} from "notistack";
import SubmitAction from "../../../components/SubmitAction";
import {DefaultForm} from "../../../components/DefaultForm";



interface IFormInputs {
    name: string,
    description: string,
    is_active: boolean
}

const SchemaValidation = yup.object({
    name: yup.string()
        .label('Nome')
        .required()
        .max(255)
}).defined();

const Form = () => {

    const history = useHistory();
    const {id} = useParams<{id: string}>();
    const snackbar = useSnackbar();

    const [category,setCategory] = useState<{id: string} | null>(null)
    const [loading, setLoading] = useState<boolean>(false)

    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch,
        trigger
    } = useForm<IFormInputs>({
       resolver: yupResolver(SchemaValidation),
       defaultValues: {
            is_active: true
       }
    });



    useEffect(() => {
        if(!id){
            return;
        }
        async function getCategory(){
            setLoading(true)
            try{
                const {data} = await categoryHttp.get(id);
                setCategory(data.data)
                reset(data.data);
            }catch (error){
                console.error(error);
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    {variant: 'error'}
                )
            }finally {
                setLoading(false)
            }
        }
        getCategory()

    },[id,reset,snackbar])

    useEffect(() => {
        register('is_active')
    },[register])


    async function onSubmit(formData,event){
        setLoading(true);
        try{
            const http = !category
                ?categoryHttp.create(formData)
                :categoryHttp.update(category.id, formData)
            const {data} = await http;
            snackbar.enqueueSnackbar(
                'Categoria Salva com Sucesso',
                {variant: 'success'}
            )
            setTimeout(() => {
                event ? (
                        id
                            ? history.replace(`/categorias/${data.data.id}/edit`)
                            : history.push(`/categorias/${data.data.id}/edit`)
                    )
                    :history.push('/categorias')
            })
        }catch(error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                'Não é possível salvar a categoria',
                {variant: 'error'}
            )
        }finally{
            setLoading(false)
        }
    }
    return (
        <DefaultForm onSubmit={handleSubmit(onSubmit)}>
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
                name={"description"}
                label={"Descrição"}
                multiline
                rows={4}
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                inputRef={register}
                InputLabelProps={{
                    shrink: true
                }}
                disabled={loading}
            />
            <FormControlLabel
                disabled={loading}
                control={
                    <Checkbox
                        name={'is_active'}
                        color={'primary'}
                        onChange={
                            () => setValue('is_active', !getValues()['is_active'])
                        }
                        checked={watch('is_active')}
                    />
                }
                label={'Ativo?'}
            />
            <SubmitAction
                disabledButtons={loading}
                handleSalve={() => {
                        trigger().then((valid) => {
                            valid && onSubmit(getValues(), null)
                        })
                    }
                }
            />
        </DefaultForm>
    );
};

export default Form;