const yup = require('yup');

//{name: '', description: ''}

// const schema = yup.object().shape({
//     name: yup
//         .string()
//         .required(),
//     num: yup.number().transform()
// });
//
// console.log(schema.cast({name: 'teste', num: "2"}));
//
// schema
//     .isValid({name: 'teste', num: "2"})
//     .then(isValid => console.log(isValid));
//
// schema.validate({name: 'teste', num: "aaa"})
//     .then((values) => console.log(values))
//     .catch(errors => console.log(errors));


const columns = [
    {
        name: 'id',
        label: 'ID',
        width: '30%',
        options: {
            sort: false
        }
    },
    {
        name: "name",
        label: "Nome",
        width: '43%'
    },
    {
        name: "is_active",
        label: "Ativo?",
        width: '4%',
    },
    {
        name: "created_at",
        label: "Criado em",
        width: '10%',
    },
    {
        name: "actions",
        label: "Ações",
        width: '13%',
    }
];

const schema = yup.object().shape({
    // pagination: yup.object().shape({
    //     page: yup.number()
    //         .transform(value => isNaN(value) || parseInt(value) < 1 ? undefined : value)
    //         .default(1),
    //     per_page: yup.number()
    //         .oneOf([10, 15, 10])
    //         .transform(value => isNaN(value) ? undefined : value)
    //         .default(15),
    // }),
    per_page: yup.number()
        .oneOf(['10', 15, 10])
        .transform(value => value)
        .default(15),
    // pagination: yup.object().shape({
    //     page: yup.number()
    //         .transform(value => isNaN(value) || parseInt(value) < 1 ? undefined : value)
    //         .default(1),
    //     per_page: yup.number()
    //         .oneOf([10, 15, 10])
    //         .transform(value => isNaN(value) ? undefined : value)
    //         .default(15),
    // }),
    // search: yup.string()
    //     .transform(value => !value ? undefined : value)
    //     .default(''),
    // pagination: yup.object().shape({
    //     page: yup.number()
    //         .transform(value => isNaN(value) || parseInt(value) < 1 ? undefined : value)
    //         .default(1),
    //     per_page: yup.number()
    //         .oneOf([10, 15, 10])
    //         .transform(value => isNaN(value) ? undefined : value)
    //         .default(15),
    // }),
    // order: yup.object().shape({
    //     sort: yup.string()
    //         .nullable()
    //         .transform(value => {
    //             const columnsName = columns
    //                 .filter(column => !column.options || column.options.sort !== false)
    //                 .map(column => column.name);
    //             return columnsName.includes(value) ? value : undefined;
    //         })
    //         .default(null),
    //     dir: yup.string()
    //         .nullable()
    //         .transform(value => !value || !['asc', 'desc'].includes(value.toLowerCase()) ? undefined : value)
    //         .default(null),
    // })
});
const teste = yup.number()
    .oneOf([10, 15, 10])
    //.transform(value => isNaN(value) ? undefined : value)
    //.default(15);
console.log(teste.cast('a'))
console.log(schema.cast({
    per_page: 25
}));
