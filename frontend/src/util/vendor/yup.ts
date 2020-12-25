import {setLocale} from "yup";

const ptBR = {
    mixed: {
        required: '${path} é requerido'
    },
    string: {
        max: '${path} precisa ter no máximo ${max} caracteres'
    },
    number:{
        min: '${path} precisa ser no mínimo ${min}'
    }
};

setLocale(ptBR);

export * from 'yup';