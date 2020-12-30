import React, {useEffect, useReducer, useRef, useState} from 'react';
import format from "date-fns/format"
import parseISO from "date-fns/parseISO"
import categoryHttp from "../../../util/http/category-http";
import {BadgeYes,BadgeNo} from '../../../components/Badge'
import {Category, ListResponse} from "../../../util/models";
import DefaultTable, {makeActionStyles, TableColumn} from '../../../components/Table'
import {useSnackbar} from "notistack";
import {IconButton, MuiThemeProvider} from "@material-ui/core";
import {Edit} from "@material-ui/icons";
import {Link} from "react-router-dom";
import FilterResetButton from "../../../components/Table/FilterResetButton";



interface Pagination{
    page: number;
    total: number;
    per_page: number;
}

interface Order{
    sort : string | null;
    dir : string | null;

}

interface SearchState  {
    search: any;
    pagination: Pagination,
    order: Order
}

const columnsDefinition: TableColumn[] = [
    {
        name: "id",
        label: "Id",
        width: "33%",
        options:{
            sort: false
        }
    },
    {
        name: "name",
        label: "Nome",
        width: "40%"
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue ){
                return value ? <BadgeYes/> : <BadgeNo/>
            }
        },
        width: '4%'
    },
    {
        name: "created_at",
        label: "Criado em",
        width: '10%',
        options: {
            customBodyRender(value, tableMeta, updateValue ){
                return <span>{format(parseISO(value),'dd/MM/yyyy')}</span>
            }
        }
    },
    {
        name: "actions",
        label: "Ações",
        width: '13%',
        options: {
            customBodyRender(value, tableMeta, updateValue): JSX.Element {
                return(
                    <IconButton
                        color={'secondary'}
                        component={Link}
                        to={`/categorias/${tableMeta.rowData[0]}/edit`}
                    >
                        <Edit/>
                    </IconButton>
                )
            }
        }

    }
];

const INITIAL_STATE = {
    search: '',
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

function reducer(state, action){
    switch (action.type){
        case 'search':
            return {
                ...state,
                search: action.search,
                pagination:{
                    ...state.pagination,
                    page: 1
                }
            };
        case 'page':
            console.log(action)
            return {
                ...state,
                pagination:{
                    ...state.pagination,
                    page: action.page
                }
            }
        case 'per_page':
            return {
                ...state,
                pagination:{
                    ...state.pagination,
                    per_page: action.per_page
                }
            }
        case 'order':
            return{
                ...state,
                order: {
                    sort: action.sort,
                    dir: action.dir
                }
            }
        default:
            return INITIAL_STATE;
            break;
    }
}


const Table = () => {
    const snackbar = useSnackbar();
    const subscribed = useRef(true);
    const [data,setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [searchState, dispatch] = useReducer(reducer,INITIAL_STATE);
    //const [searchState, setSearchState] = useState<SearchState>(initialState)
    console.log(searchState)
    useEffect(() => {
        subscribed.current = true;
        getData();
        return () => {
            subscribed.current = false;
        }
    },[
        searchState.search,
        searchState.pagination.page,
        searchState.pagination.per_page,
        searchState.order
    ]);


    async function getData(){
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>({
                queryParam: {
                    search: clearSearchText(searchState.search),
                    page: searchState.pagination.page,
                    per_page: searchState.pagination.per_page,
                    sort: searchState.order.sort,
                    dir: searchState.order.dir
                }
            });
            if(subscribed.current){
                    setData(data.data)
                // setSearchState((prevState => ({
                //   ...prevState,
                //     pagination: {
                //       ...prevState.pagination,
                //         total: data.meta.total
                //     }
                // })))
            }
            }catch (error){
                console.error(error);
                if(categoryHttp.isCancelledRequest(error)){
                    return;
                }
                snackbar.enqueueSnackbar(
                    'Não foi possível carregar as informações',
                    {variant: 'error'}
                )
            }finally {
                setLoading(false)
            }
        }

        function clearSearchText(text){
            let newText = text;
            if(text && text.value !== undefined){
                newText = text.value;
            }
            return newText;
        }

        return (
        <MuiThemeProvider theme={makeActionStyles(columnsDefinition.length - 1)}>
            <DefaultTable
                columns={columnsDefinition}
                data={data}
                title={'Listagem de Categorias'}
                loading={loading}
                debouncedSearchTime={500}
                options={{
                    serverSide: true,
                    searchText: searchState.search,
                    page: searchState.pagination.page - 1,
                    rowsPerPage: searchState.pagination.per_page,
                    count: searchState.pagination.total,
                    customToolbar: () => (
                        <FilterResetButton handleClick={() => dispatch({type: 'reset'})}/>
                    ),
                    onSearchChange: (value) => dispatch({type: 'search', search: value}),
                    onChangePage: (page) => dispatch({type: 'page', page: page + 1}),
                    onChangeRowsPerPage: (perPage) => dispatch({type: 'page', per_page: perPage}),
                    onColumnSortChange: (changedColumn: string, direction: string) => dispatch({
                        type: 'order',
                        sort: changedColumn,
                        dir: direction
                    })
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;