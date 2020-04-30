const {createStore, applyMiddleware} = require('redux');
const {default: createSagaMiddleware} = require('redux-saga');
const {take, put, call, actionChannel, debounce, select, all, fork} = require('redux-saga/effects');
const axios = require('axios');

function reducer(state = {value: 1}, action) {
    if (action.type === 'acaoY') {
        return {...state, text: action.value}
    }
    if (action.type === 'acaoX') {
        return {value: action.value};
    }

    return state;
}

function* sagaNonBlocing(){
    console.log('antes do call');
    const {data} = yield call(  //(search) => axios.get('http://nginx/api/videos?search='+search),search
        axios.get, 'http://nginx/api/videos'
    );
    console.log('depois do call');
    //yield put()
}

function* searchData(action) { //type, value
    //console.log('Hello World');
    //const channel = yield actionChannel('acaoY');
    //console.log(channel);
    //while (true) {
    console.log(yield select((state) => state.text));
    console.log('antes da acao Y');
    //const action = yield take(channel); //todas as action types
    const search = action.value;
    try {


        yield fork(sagaNonBlocing);
        console.log('depois do fork');
        // const [response1, response2] = yield all([
        //     call(  //(search) => axios.get('http://nginx/api/videos?search='+search),search
        //         axios.get, 'http://nginx/api/videos?search=' + search
        //     ),
        //     call(
        //         //(search) => axios.get('http://nginx/api/videos?search='+search),search
        //         axios.get, 'http://nginx/api/categories?search=' + search
        //     )
        // ]);
        //console.log(response1.data.data.length, response2.data.data.length);
        // const {data} = yield call(
        //     //(search) => axios.get('http://nginx/api/videos?search='+search),search
        //     axios.get, 'http://nginx/api/videos?search=' + search
        // );
        //
        // const {data1} = yield call(
        //     //(search) => axios.get('http://nginx/api/videos?search='+search),search
        //     axios.get, 'http://nginx/api/categories?search=' + search
        // );
        console.log(search);
        //console.log(data);
        yield put({
            type: 'acaoX',
            value: ''
        });
    } catch (e) {
        console.log(e);
        yield put({
            type: 'acaoX',
            error: e
        });
    }
    //}

    //proxima
    //console.log(result);

    //PEGAR OS DADOS DE ACTION TYPE
    //FAZER UMA REQUISIÇÃO AJAX
    //ATUALIZAR MEU STATE
}

function* helloWorld(){
    console.log('Hello World');
}

function* debounceSearch() {
    yield debounce(1000, 'acaoY', searchData)
}

function* rootSaga(){
    yield all([
        helloWorld(),
        debounceSearch()
    ])

    // yield fork(helloWorld);
    // yield fork(debounceSearch);
    //
    // console.log('final');
    //outras coisas
}

// const generator = helloWorldSaga();
// generator.next()
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
    reducer,
    applyMiddleware(sagaMiddleware)
);
sagaMiddleware.run(rootSaga);

const action = (type, value) => store.dispatch({type, value});

action('acaoY', 'l');//esperar mudar o state
action('acaoY', 'lui');
action('acaoY', 'luiz');
action('acaoY', 'luiz c');
action('acaoY', 'luiz ca');
//action('acaoW', 'a');
//o state ainda mudou
console.log(store.getState());
