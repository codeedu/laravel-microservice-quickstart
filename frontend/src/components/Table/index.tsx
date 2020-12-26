import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps} from "mui-datatables";
import {merge,omit} from 'lodash';

export interface TableColumn extends MUIDataTableColumn{
    width?: string
}

const defaultOptions: MUIDataTableOptions = {
    print: false,
    download: false,
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
    columns: TableColumn[]
}


const Table: React.FC<TableProps> = (props) => {

    function extractMuiDataTableColumns(columns: TableColumn[]): MUIDataTableColumn[]{
        //return columns.map(column => omit(column, 'width'));
        return columns;
    }


    const newMerge = merge(
        {options: defaultOptions},
        props,
        {columns: extractMuiDataTableColumns(props.columns)}
    );
    console.log(newMerge);
    return (
        <MUIDataTable {...newMerge}/>
    );
};

export default Table;