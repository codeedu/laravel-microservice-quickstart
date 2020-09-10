const { createStore, applyMiddleware } = require('redux');

function reducer(state, action){
    console.log('reducer');
    return {value: action.value};
}

const customMiddleware = store => next => action => {
    if(action.type === 'acaoX'){
        next({type: action.type, value: 'xpto'});
        return;
    }
    console.log(action);
    //assincrona
    next(action);
    console.log('Luiz Carlos');
};

const store = createStore(
    reducer,
    //applyMiddleware(customMiddleware)
);

const action = (type, value) => store.dispatch({type, value});

action('acaoX', 'a');

console.log(store.getState());
