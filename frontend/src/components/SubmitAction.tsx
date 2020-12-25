import React from 'react';
import {Box, Button, ButtonProps} from "@material-ui/core";
import {makeStyles, Theme} from "@material-ui/core/styles";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

interface SubmitActionProps{
    disabledButtons: boolean,
    handleSalve: () => void
}

const SubmitAction: React.FC<SubmitActionProps> = (props) => {
    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "contained",
        color: 'secondary',
        disabled: props.disabledButtons
    }

    return (
        <Box dir={'rtl'}>
            <Button
                color={'primary'}
                {...buttonProps}
                onClick={props.handleSalve}
            >
                Salvar
            </Button>
            <Button
                {...buttonProps}
                type={'submit'}>
                Salvar e continuar editando
            </Button>
        </Box>
    );
};

export default SubmitAction;