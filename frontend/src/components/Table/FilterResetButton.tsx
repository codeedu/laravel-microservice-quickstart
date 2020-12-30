import React from 'react';
import {IconButton, Tooltip} from "@material-ui/core";
import {ClearAll} from "@material-ui/icons";
import {makeStyles} from "@material-ui/core/styles";


const useStyles = makeStyles(theme => ({
    iconButton: (theme as any).overrides.MUIDataTableToolbar.icon
}))

interface FilterResetButton {
    handleClick: () => void
}

const FilterResetButton: React.FC<FilterResetButton> = (props) => {
    const classes = useStyles();
    return (
        <Tooltip title={'Limpar Busca'}>
            <IconButton className={classes.iconButton} onClick={props.handleClick}>
                <ClearAll/>
            </IconButton>
        </Tooltip>
    );
};

export default FilterResetButton;