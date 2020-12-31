import * as Typings from './types';
import {createActions, createReducer} from 'reduxsauce';

export const {Types, Creators} = createActions<{
    SET_SEARCH: string,
    SET_PAGE: string,
    SET_PER_PAGE: string,
    SET_ORDER: string,
    SET_RESET: string
},{
    setSearch(payload: Typings.SetSearchAction['payload']): Typings.SetSearchAction
    setPage(payload: Typings.SetPageAction['payload']): Typings.SetPageAction
    setPerPage(payload: Typings.SetPerPageAction['payload']): Typings.SetPerPageAction
    setOrder(payload: Typings.SetOrderAction['payload']): Typings.SetOrderAction,
    setReset()
}>({
    setSearch: ['payload'],
    setPage: ['payload'],
    setPerPage: ['payload'],
    setOrder: ['payload'],
    setReset:[]
})

export const INITIAL_STATE: Typings.State = {
    search: null,
    pagination: {
        page: 1,
        total: 0,
        per_page: 10
    },
    order: {
        sort: null,
        dir: null
    }
}

const reducer = createReducer<Typings.State, Typings.Action>(INITIAL_STATE,{
    [Types.SET_SEARCH]: setSearch,
    [Types.SET_PAGE]: setPage,
    [Types.SET_PER_PAGE]: setPerPage,
    [Types.SET_ORDER]: setOrder,
    [Types.SET_RESET]: setReset
})

export default reducer;

function setSearch(state = INITIAL_STATE, action: Typings.SetSearchAction): Typings.State{
    return{
        ...state,
        search: action.payload.search,
        pagination:{
            ...state.pagination,
            page: 1
        }
    }
}

function setPage(state = INITIAL_STATE, action: Typings.SetPageAction): Typings.State{
    return{
        ...state,
        pagination:{
            ...state.pagination,
            page: action.payload.page
        }
    }
}

function setPerPage(state = INITIAL_STATE, action: Typings.SetPerPageAction): Typings.State{
    return{
        ...state,
        pagination:{
            ...state.pagination,
            per_page: action.payload.per_page
        }
    }
}

function setOrder(state = INITIAL_STATE, action: Typings.SetOrderAction): Typings.State{
    return{
        ...state,
        order: {
            sort: action.payload.sort,
            dir: action.payload.dir
        }
    }
}

function setReset(state = INITIAL_STATE, action){
    return {...INITIAL_STATE, search: {value: null, update: true}}
}



