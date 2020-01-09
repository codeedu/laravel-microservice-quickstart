const {createActions} = require('reduxsauce');

//import {createActions} from 'reduxsauce';


const {Types, Creators} = createActions({
    setSearch: ['payload'],
    setPage: ['payload'],
    setPerPage: ['payload'],
    setOrder: ['payload'],
});

console.log(Types, Creators);

//dispatch(createdActions.addParam({search}))
console.log(Creators.setSearch({search: 'teste'}));
//dispatch(Creators.setSearch({search: 'teste'}));


