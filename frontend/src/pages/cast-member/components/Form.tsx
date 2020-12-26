import React, {useEffect, useState} from "react";
import {
    FormControl,
    FormControlLabel, FormHelperText,
    FormLabel,
    Radio,
    RadioGroup,
    TextField
} from "@material-ui/core";
import {useForm} from "react-hook-form";
import castMemberHttp from "../../../util/http/cast-member-http";
import * as yup from "../../../util/vendor/yup";
import { yupResolver } from '@hookform/resolvers/yup';
import {useHistory, useParams} from "react-router";
import {useSnackbar} from "notistack";
import SubmitAction from "../../../components/SubmitAction";
import {DefaultForm} from "../../../components/DefaultForm";

interface IFormInputs {
    name: string,
    type: number
}

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
    const history = useHistory();
    const {id} = useParams<{id: string}>();
    const snackbar = useSnackbar();

    const [castMember,setcastMember] = useState<{id: string} | null>(null)
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
        <DefaultForm onSubmit={handleSubmit(onSubmit)}>
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