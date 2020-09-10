import * as Typings from "./types";
import {createActions, createReducer} from 'reduxsauce';
import {SetOrderAction, SetPerPageAction, SetResetAction, UpdateExtraFilterAction} from "./types";

export const {Types, Creators} = createActions<{
    SET_SEARCH: string,
    SET_PAGE: string,
    SET_PER_PAGE: string,
    SET_ORDER: string,
    SET_RESET: string,
    UPDATE_EXTRA_FILTER: string
}, {
    setSearch(payload: Typings.SetSearchAction['payload']): Typings.SetSearchAction
    setPage(payload: Typings.SetPageAction['payload']): Typings.SetPageAction
    setPerPage(payload: Typings.SetPerPageAction['payload']): Typings.SetPerPageAction
    setOrder(payload: Typings.SetOrderAction['payload']): Typings.SetOrderAction,
    setReset(payload: Typings.SetResetAction['payload']): Typings.SetResetAction,
    updateExtraFilter(payload: Typings.UpdateExtraFilterAction['payload']): Typings.UpdateExtraFilterAction
}>
({
    setSearch: ['payload'],
    setPage: ['payload'],
    setPerPage: ['payload'],
    setOrder: ['payload'],
    setReset: ['payload'],
    updateExtraFilter: ['payload'],
});

export const INITIAL_STATE: Typings.State = {
    search: null,
    pagination: {
        page: 1,
        per_page: 10
    },
    order: {
        sort: null,
        dir: null
    },
};

const reducer = createReducer<Typings.State, Typings.Actions>(INITIAL_STATE, {
    [Types.SET_SEARCH]: setSearch as any,
    [Types.SET_PAGE]: setPage as any,
    [Types.SET_PER_PAGE]: setPerPage as any,
    [Types.SET_ORDER]: setOrder as any,
    [Types.SET_RESET]: setReset as any,
    [Types.UPDATE_EXTRA_FILTER]: updateExtraFilter as any
});

export default reducer;

function setSearch(state = INITIAL_STATE, action: Typings.SetSearchAction): Typings.State {
    return {
        ...state,
        search: action.payload.search,
        pagination: {
            ...state.pagination,
            page: 1
        }
    };
}


function setPage(state = INITIAL_STATE, action: Typings.SetPageAction): Typings.State {
    return {
        ...state,
        pagination: {
            ...state.pagination,
            page: action.payload.page,
        }
    };
}

function setPerPage(state = INITIAL_STATE, action: SetPerPageAction): Typings.State {
    return {
        ...state,
        pagination: {
            ...state.pagination,
            per_page: action.payload.per_page
        }
    };
}

function setOrder(state = INITIAL_STATE, action: SetOrderAction): Typings.State {
    return {
        ...state,
        pagination: {
            ...state.pagination,
            page: 1
        },
        order: {
            sort: action.payload.sort, dir: action.payload.dir
        }
    }
}

function setReset(state = INITIAL_STATE, action: SetResetAction) {
    return action.payload.state;
}

function updateExtraFilter(state = INITIAL_STATE, action: UpdateExtraFilterAction){
    return {
        ...state,
        extraFilter: {
            ...state.extraFilter,
            ...action.payload // {type: 'Diretor'}
        }
    }
}
//historicos dos estados conforme o usuário vai mexendo na aplicação
//log, debug, outras tarefas, assincrono, erros - middlewares - Redux Thunk ou Redux Saga
//separar os motivos da mudanças de como elas são feitas
