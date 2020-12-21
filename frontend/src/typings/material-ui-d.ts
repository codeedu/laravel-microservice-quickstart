import {ComponentNameToClassKey} from '@material-ui/core/styles/overrides'

declare module '@material-ui/core/styles/overrides'{
    interface ComponentNameToClassKey{
        MUIDataTable: any;
        MUIDataTableRoolbar: any;
        MUIDataTableHeadCell: any;
        MUIDataTableSelectCell: any;
        MUIDataTableBodyCell: any;
        MUIDataTableToolbar: any;
        MUIDataTableToolbarSelect: any;
        MUIDataTableBodyRow: any;
        MUIDataTablePagination: any;
    }
}