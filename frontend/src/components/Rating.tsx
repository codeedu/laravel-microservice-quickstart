import * as React from 'react';
import {makeStyles, Typography} from "@material-ui/core";

const useStyles = makeStyles({
    root: {
        width: '36px',
        height: '36px',
        fontSize: '1.2em',
        color: '#fff',
        borderRadius: '4px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    }
});

const backgroundColors = {
    'L': '#39B549',
    '10': '#20A3D4',
    '12': '#E79738',
    '14': '#E35E00',
    '16': '#d00003',
    '18': '#000000'
};

interface RatingProps {
    rating: 'L' | '10' | '12' | '14' | '16' | '18'
}


const Rating: React.FC<RatingProps> = (props) => {
    const classes = useStyles();
    return (
        <Typography
            className={classes.root}
            style={{
                backgroundColor: backgroundColors[props.rating]
            }}
        >
            {props.rating}
        </Typography>
    );
};


export default Rating;
