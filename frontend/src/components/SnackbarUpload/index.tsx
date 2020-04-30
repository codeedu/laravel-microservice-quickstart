import * as React from 'react';
import {
    Card,
    CardActions,
    Collapse,
    IconButton,
    List, Theme,
    Typography
} from "@material-ui/core";
import CloseIcon from '@material-ui/icons/Close';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import {useSnackbar} from "notistack";
import {makeStyles} from "@material-ui/core/styles";
import {useState} from "react";
import classnames from "classnames";
import UploadItem from "./UploadItem";
import {useSelector} from "react-redux";
import {Upload, UploadModule} from "../../store/upload/types";
import {countInProgress} from "../../store/upload/getters";

const useStyles = makeStyles((theme: Theme) => ({
    card: {
        width: 450,
    },
    cardActionRoot: {
        padding: '8px 8px 8px 16px',
        backgroundColor: theme.palette.primary.main,
    },
    title: {
        fontWeight: 'bold',
        color: theme.palette.primary.contrastText,
    },
    icons: {
        marginLeft: 'auto !important',
        color: theme.palette.primary.contrastText,
    },
    expand: {
        transform: 'rotate(0deg)',
        transition: theme.transitions.create('transform', {
            duration: theme.transitions.duration.shortest
        })
    },
    expandOpen: {
        transform: 'rotate(180deg)',
        transition: theme.transitions.create('transform', {
            duration: theme.transitions.duration.shortest
        })
    },
    list: {
        paddingTop: 0,
        paddingBottom: 0
    }
}));

interface SnackbarUploadProps {
    id: string | number;
}

const SnackbarUpload = React.forwardRef<any, SnackbarUploadProps>((props, ref) => {

    const {id} = props;
    const classes = useStyles();
    const {closeSnackbar} = useSnackbar();
    const [expanded, setExpanded] = useState(true);

    const uploads = useSelector<UploadModule, Upload[]>(
        (state) => state.upload.uploads
    );

    const totalInProgress = countInProgress(uploads);

    return (
        <Card ref={ref} className={classes.card}>
            <CardActions classes={{root: classes.cardActionRoot}}>
                <Typography variant="subtitle2" className={classes.title}>
                    Fazendo upload de {totalInProgress} vídeo(s)
                </Typography>
                <div className={classes.icons}>
                    <IconButton
                        color={"inherit"}
                        onClick={() => setExpanded(!expanded)}
                        className={classnames(classes.expand, {[classes.expandOpen]: !expanded})}
                    >
                        <ExpandMoreIcon/>
                    </IconButton>
                    <IconButton
                        color={"inherit"}
                        onClick={() => closeSnackbar(id)}
                    >
                        <CloseIcon/>
                    </IconButton>
                </div>
            </CardActions>
            <Collapse in={expanded}>
                <List className={classes.list}>
                    {
                        uploads.map((upload, key) => (
                            <UploadItem key={key} upload={upload}/>
                        ))
                    }
                </List>
            </Collapse>
        </Card>
    );
});

export default SnackbarUpload;
