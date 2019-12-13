import * as React from 'react';
import {Box, Button, Checkbox, FormControlLabel, makeStyles, TextField, Theme} from "@material-ui/core";
import {ButtonProps} from "@material-ui/core/Button";
import useForm from "react-hook-form";
import categoryHttp from "../../util/http/category-http";
import * as yup from '../../util/vendor/yup';
import {useEffect, useState} from "react";
import {useParams, useHistory} from "react-router";
import {useSnackbar} from "notistack";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string()
        .label('Nome')
        .required()
        .max(255),
});

export const Form = () => {
    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch
    } = useForm({
        validationSchema,
        defaultValues: {
            is_active: true
        }
    });

    const classes = useStyles();
    const snackbar = useSnackbar();
    const history = useHistory();
    const {id} = useParams();
    const [category, setCategory] = useState<{ id: string } | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: 'secondary',
        variant: 'contained',
        disabled: loading
    };

    useEffect(() => {
        if (!id) {
            return;
        }

        async function getCategory() {
            setLoading(true);
            try {
                const {data} = await categoryHttp.get(id);
                setCategory(data.data);
                reset(data.data);
            } catch (error) {
                console.error(error);
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    {variant: 'error',}
                )
            } finally {
                setLoading(false);
            }
        }

        getCategory();
    }, []);

    useEffect(() => {
        register({name: "is_active"})
    }, [register]);

    async function onSubmit(formData, event) {
        setLoading(true);
        try {
            const http = !category
                ? categoryHttp.create({})
                : categoryHttp.update(category.id, formData);
            const {data} = await http;
            snackbar.enqueueSnackbar(
                'Categoria salva com sucesso',
                {variant: 'success'}
            );
            setTimeout(() => {
                event
                    ? (
                        id
                            ? history.replace(`/categories/${data.data.id}/edit`)
                            : history.push(`/categories/${data.data.id}/edit`)
                    )
                    : history.push('/categories')
            });
        } catch (error) {
            console.error(error);
            snackbar.enqueueSnackbar(
                'Não foi possível salvar a categoria',
                {variant: 'error'}
            )
        } finally {
            setLoading(false)
        }
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                inputRef={register}
                disabled={loading}
                error={errors.name !== undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
            />
            <TextField
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                inputRef={register}
                disabled={loading}
                InputLabelProps={{shrink: true}}
            />
            <FormControlLabel
                disabled={loading}
                control={
                    <Checkbox
                        name="is_active"
                        color={"primary"}
                        onChange={
                            () => setValue('is_active', !getValues()['is_active'])
                        }
                        checked={watch('is_active')}
                    />
                }
                label={'Ativo?'}
                labelPlacement={'end'}
            />
            <Box dir={"rtl"}>
                <Button
                    color={"primary"}
                    {...buttonProps}
                    onClick={() => onSubmit(getValues(), null)}
                >
                    Salvar
                </Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};
