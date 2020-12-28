import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps} from "mui-datatables";
import {merge,omit, cloneDeep} from 'lodash';
import {useTheme, Theme, MuiThemeProvider} from "@material-ui/core";


export interface TableColumn extends MUIDataTableColumn{
    width?: string
}

const defaultOptions: MUIDataTableOptions = {
    print: false,
    download: false,
    responsive: 'simple',
    textLabels:{
        body:{
            noMatch: 'Nenhum registro encontrado',
            toolTip: 'Classificar'
        },
        pagination:{
            next: 'Próximo',
            previous: 'Anterior',
            rowsPerPage: 'Por página',
            displayRows: 'de'
        },
        toolbar:{
            search: 'Busca',
            downloadCsv: 'Download CSV',
            print: 'Imprimir',
            viewColumns: 'Ver Colunas',
            filterTable: 'Filtrar tabela'
        },
        filter:{
            all: 'Todos',
            title: 'Filtros',
            reset: 'Limpar'
        },
        viewColumns:{
            title: 'Ver Colunas',
            titleAria: 'Ver/Esconder'
        },
        selectedRows:{
            delete: 'Excluir',
            deleteAria: 'Excluir registros selecionados',
            text: 'registro(s) selecionados'
        }
    }
}

interface TableProps extends MUIDataTableProps{
    columns: TableColumn[],
    loading?: boolean
}


const Table: React.FC<TableProps> = (props) => {

    function extractMuiDataTableColumns(columns: TableColumn[]): Pick<TableColumn, never>[]{
        setColumnsWithColumns(columns);
        return columns.map(column => omit(column, 'width'));
    }

    function setColumnsWithColumns(columns: TableColumn[]){
        columns.forEach((columns,key) => {
            if(columns.width){
                const overrides = theme.overrides as any;
                overrides.MUIDataTableHeadCell.fixedHeader[`&:nth-child(${key +2})`] = {
                    width: columns.width
                }
            }
        })
    }


    function getOriginalMuiDataTableProps(){
        return omit(newProps,'loading');
    }

    function applyLoading(){
        const textLabels = (newProps.options as any).textLabels;
        textLabels.body.noMatch = newProps.loading === true
        ? 'Carregando...' : textLabels.body.noMatch
    }

    const theme = cloneDeep<Theme>(useTheme());
    const newProps = merge(
        {options: cloneDeep(defaultOptions)},
        props,
        {columns: extractMuiDataTableColumns(props.columns)}
    );

    applyLoading();
    getOriginalMuiDataTableProps();

    return (
        <MuiThemeProvider theme={theme}>
            <MUIDataTable {...newProps}/>
        </MuiThemeProvider>
    );
};

export default Table;

export function makeActionStyles(column){
    return theme => {
        const copyTheme = cloneDeep(theme);
        const selector = `&[data-testid^="MuiDataTableBodyCell-${column}"]`;
        (copyTheme.overrides as any).MUIDataTableBodyCell.root[selector] = {
            paddingTop: '0px',
            paddingBottom: '0px'
        }
        return copyTheme;
    }
}