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
import reducer, {INITIAL_STATE,Creators} from "../../../store/filter";
import useFilter from "../../../hooks/useFilter";


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
     const {
         filterState,
         dispatch,
         totalRecords,
         setTotalRecords
     } = useFilter();

    useEffect(() => {
        subscribed.current = true;
        getData();
        return () => {
            subscribed.current = false;
        }
    },[
        filterState.search,
        filterState.pagination.page,
        filterState.pagination.per_page,
        filterState.order
    ]);


    async function getData(){
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>({
                queryParam: {
                    search: clearSearchText(filterState.search),
                    page: filterState.pagination.page,
                    per_page: filterState.pagination.per_page,
                    sort: filterState.order.sort,
                    dir: filterState.order.dir
                }
            });
            if(subscribed.current){
                setData(data.data)
                setTotalRecords(data.meta.total)
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
                    searchText: filterState.search as any,
                    page: filterState.pagination.page - 1,
                    rowsPerPage: filterState.pagination.per_page,
                    count: totalRecords,
                    customToolbar: () => (
                        <FilterResetButton handleClick={() => {
                            dispatch(Creators.setReset())
                        }}/>
                    ),
                    onSearchChange: (value) => dispatch(Creators.setSearch({search: value})),
                    onChangePage: (page) => dispatch(Creators.setPage({page: page + 1})),
                    onChangeRowsPerPage: (perPage) => dispatch(Creators.setPerPage({per_page: perPage})),
                    onColumnSortChange: (changedColumn: string, direction: string) => dispatch(Creators.setOrder({
                        dir: direction,
                        sort: changedColumn
                    }))
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;