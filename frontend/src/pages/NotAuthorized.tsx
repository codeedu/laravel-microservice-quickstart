import * as React from 'react';
import Typography from "@material-ui/core/Typography";
import {Box, Container, createStyles, makeStyles, Theme} from "@material-ui/core";
import {Link} from "react-router-dom";
import ExitToAppIcon from '@material-ui/icons/ExitToApp';

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        paragraph: {
            display: 'flex',
            margin: theme.spacing(2),
            alignItems: 'center'
        },
    }),
);

export const NotAuthorized: React.FC = (props) => {
    const classes = useStyles();
    return (
        <Container>
            <Typography variant="h4" component="h1">
                403 - Acesso não autorizado
            </Typography>

            <Box className={classes.paragraph}>
                <ExitToAppIcon/>
                <Typography >
                    Acesse o Codeflix pelo <Link to={'/'}>endereço</Link>
                </Typography>
            </Box>

        </Container>
    );
};
