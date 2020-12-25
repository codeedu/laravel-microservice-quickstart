import React, {useEffect, useState} from "react";
import {
    Box,
    Button,
    ButtonProps,
    FormControl,
    FormControlLabel, FormHelperText,
    FormLabel,
    Radio,
    RadioGroup,
    TextField
} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import castMemberHttp from "../../../util/http/cast-member-http";
import * as yup from "../../../util/vendor/yup";
import { yupResolver } from '@hookform/resolvers/yup';
import {useHistory, useParams} from "react-router";
import {useSnackbar} from "notistack";


interface IFormInputs {
    name: string,
    type: number
}

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const SchemaValidation = yup.object({
    name: yup.string()
        .label('Nome')
        .required()
        .max(255),
    type: yup.number()
        .label('Tipo')
        .required()
}).defined();


const Form = () => {
    const classes = useStyles();
    const history = useHistory();
    const {id} = useParams<{id: string}>();
    const snackbar = useSnackbar();

    const [castMember,setcastMember] = useState<{id: string} | null>(null)
    const [loading, setLoading] = useState<boolean>(false)

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: "secondary",
        variant: "contained"
    }



    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch
    } = useForm<IFormInputs>({
        resolver: yupResolver(SchemaValidation),
    });

    useEffect(() => {
        register({name: 'type'})
    },[register])

    useEffect(() => {
        if(!id){
            return;
        }
        async function getCastMember(){
            setLoading(true)
            try{
                const {data} = await castMemberHttp.get(id);
                setcastMember(data.data)
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
        getCastMember()
    },[id,reset,snackbar]);


    async function onSubmit(formData,event){
        setLoading(true);
        try{
            const http = !castMember
                ?castMemberHttp.create(formData)
                :castMemberHttp.update(castMember.id, formData)
            const {data} = await http;
            snackbar.enqueueSnackbar(
                'Elenco Salvo com Sucesso',
                {variant: 'success'}
            )
            setTimeout(() => {
                event ? (
                        id
                            ? history.replace(`/cast-members/${data.data.id}/edit`)
                            : history.push(`/cast-members/${data.data.id}/edit`)
                    )
                    :history.push('/cast-members')
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
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name={"name"}
                label={"Nome"}
                fullWidth
                variant={"outlined"}
                disabled={loading}
                error={errors.name !== undefined}
                helperText={errors.name?.message}
                inputRef={register}
                InputLabelProps={{
                    shrink: true
                }}
            />
            <FormControl
                margin={'normal'}
                error={errors.type !== undefined}
                disabled={loading}
            >
                <FormLabel component={'legend'}>Tipo</FormLabel>
                <RadioGroup
                    name={'type'}
                    onChange={(event => {setValue('type',parseInt(event.target.value))})}
                    value={watch('type') + ""}
                    >
                    <FormControlLabel value='1' control={<Radio/>} label={'Diretor'}/>
                    <FormControlLabel value='2' control={<Radio/>} label={'Ator'}/>
                </RadioGroup>
                {
                    errors.type && <FormHelperText id="type-helper-text">{errors.type.message}</FormHelperText>
                }
            </FormControl>
            <Box dir={'rtl'}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(),null)}>Salvar</Button>
                <Button {...buttonProps} type={'submit'}>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};

export default Form;