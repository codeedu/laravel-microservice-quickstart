import React, {useEffect, useRef, useState} from 'react';
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


const Table = () => {
    const snackbar = useSnackbar();
    const subscribed = useRef(true);
    const [data,setData] = useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [searchState, setSearchState] = useState<SearchState>({
        search: '',
        pagination: {
            page: 1,
            total: 0,
            per_page: 10
        },
        order:{
            sort: null,
            dir: null
        }
    });

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
                    search: searchState.search,
                    page: searchState.pagination.page,
                    per_page: searchState.pagination.per_page,
                    sort: searchState.order.sort,
                    dir: searchState.order.dir
                }
            });
            if(subscribed.current){
                    setData(data.data)
                setSearchState((prevState => ({
                  ...prevState,
                    pagination: {
                      ...prevState.pagination,
                        total: data.meta.total
                    }
                })))
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

        return (
        <MuiThemeProvider theme={makeActionStyles(columnsDefinition.length - 1)}>
            <DefaultTable
                columns={columnsDefinition}
                data={data}
                title={'Listagem de Categorias'}
                loading={loading}
                options={{
                    serverSide: true,
                    searchText: searchState.search,
                    page: searchState.pagination.page - 1,
                    rowsPerPage: searchState.pagination.per_page,
                    count: searchState.pagination.total,
                    onSearchChange: (value) => setSearchState((prevState => ({
                        ...prevState,
                        search: value
                    }))),
                    onChangePage: (page) => setSearchState((prevState => ({
                        ...prevState,
                        pagination:{
                            ...prevState.pagination,
                            page: page + 1
                        }
                    }))),
                    onChangeRowsPerPage: (perPage) => setSearchState((prevState => ({
                        ...prevState,
                        pagination:{
                            ...prevState.pagination,
                            page: 1,
                            per_page: perPage
                        }
                    }))),
                    onColumnSortChange: (changedColumn: string, direction: string) => setSearchState((prevState => ({
                        ...prevState,
                        order: {
                            sort: changedColumn,
                            dir: direction
                        }
                    }))),
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;