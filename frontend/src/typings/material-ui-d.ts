import {ComponentNameToClassKey} from '@material-ui/core/styles/overrides';
import {PaletteOptions,Palette,PaletteColorOptions, PaletteColor} from '@material-ui/core/styles/createPalette';


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


declare module '@material-ui/core/styles/createPalette'{
    interface Pallete{
        success: PaletteColor
    }

    interface PaletteOptions{
        success?: PaletteColorOptions
    }
 }