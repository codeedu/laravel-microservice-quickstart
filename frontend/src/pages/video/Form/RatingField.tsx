// @flow
import * as React from 'react';
import {Box, FormControl, FormControlLabel, FormHelperText, FormLabel, Radio, RadioGroup} from "@material-ui/core";
import {FormControlLabelProps} from "@material-ui/core/FormControlLabel";
import Rating from "../../../components/Rating";
import {FormControlProps} from "@material-ui/core/FormControl";

interface RatingFieldProps {
    value: string;
    setValue: (value) => void
    error: any;
    disabled?: boolean;
    FormControlProps?: FormControlProps
}

const ratings: FormControlLabelProps[] = [
    {value: "L", control: <Radio color="primary"/>, label: <Rating rating={'L'}/>, labelPlacement: "top"},
    {value: "10", control: <Radio color="primary"/>, label: <Rating rating={'10'}/>, labelPlacement: "top"},
    {value: "12", control: <Radio color="primary"/>, label: <Rating rating={'12'}/>, labelPlacement: "top"},
    {value: "14", control: <Radio color="primary"/>, label: <Rating rating={'14'}/>, labelPlacement: "top"},
    {value: "16", control: <Radio color="primary"/>, label: <Rating rating={'16'}/>, labelPlacement: "top"},
    {value: "18", control: <Radio color="primary"/>, label: <Rating rating={'18'}/>, labelPlacement: "top"},
];

const RatingField: React.FC<RatingFieldProps> = (props) => {
    const {value, setValue, error, disabled} = props;
    return (
        <FormControl
            error={error !== undefined}
            disabled={disabled === true}
            {...props.FormControlProps}
        >
            <FormLabel component="legend">Classificação</FormLabel>
            <Box paddingTop={1}>
            <RadioGroup
                name="rating"
                onChange={(e) => {
                    setValue(e.target.value);
                }}
                value={value}
                row
            >
                {
                    ratings.map(
                        (props, key) => <FormControlLabel key={key} {...props}/>
                    )
                }
            </RadioGroup>
            </Box>
            {
                error && <FormHelperText>{error.message}</FormHelperText>
            }
        </FormControl>
    );
};

export default RatingField;
