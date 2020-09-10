import React from 'react';
import Grow from '@material-ui/core/Grow';
import TextField from '@material-ui/core/TextField';
import SearchIcon from '@material-ui/icons/Search';
import IconButton from '@material-ui/core/IconButton';
import ClearIcon from '@material-ui/icons/Clear';
import {withStyles} from '@material-ui/core/styles';
import {debounce} from 'lodash';

const defaultSearchStyles = theme => ({
    main: {
        display: 'flex',
        flex: '1 0 auto',
    },
    searchIcon: {
        color: theme.palette.text.secondary,
        marginTop: '10px',
        marginRight: '8px',
    },
    searchText: {
        flex: '0.8 0',
    },
    clearIcon: {
        '&:hover': {
            color: theme.palette.error.main,
        },
    },
});

//pura - , impura
class DebouncedTableSearch extends React.PureComponent {

    constructor(props) {
        super(props);
        const {searchText} = this.props;
        let value = searchText;
        if (searchText && searchText.value !== undefined) {
            value = searchText.value;
        }
        this.state = {
            text: value
        };
        this.debouncedOnSearch = debounce(this.debouncedOnSearch.bind(this), this.props.debounceTime);
    }

    handleTextChange = event => {
        const value = event.target.value;
        console.log(value);
        this.setState({
            text: value
        }, () => this.debouncedOnSearch(value))
    };

    debouncedOnSearch = value => {
        this.props.onSearch(value)
    };

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {searchText} = this.props;
        if (searchText && searchText.value !== undefined && prevProps.searchText !== this.props.searchText) {
            const value = searchText.value;
            if (value) {
                this.setState({
                    text: value
                }, () => this.props.onSearch(value))
            } else {
                try {
                    this.props.onHide()
                } catch (e) {

                }
            }
        }
    }


    componentDidMount() {
        document.addEventListener('keydown', this.onKeyDown, false);
    }

    componentWillUnmount() {
        document.removeEventListener('keydown', this.onKeyDown, false);
    }

    onKeyDown = event => {
        if (event.keyCode === 27) {
            this.props.onHide();
        }
    };

    render() {
        const {classes, options, onHide} = this.props;
        let value = this.state.text;
        console.log(value);
        return (
            <Grow appear in={true} timeout={300}>
                <div className={classes.main} ref={el => (this.rootRef = el)}>
                    <SearchIcon className={classes.searchIcon}/>
                    <TextField
                        className={classes.searchText}
                        autoFocus={true}
                        InputProps={{
                            'data-test-id': options.textLabels.toolbar.search,
                            'aria-label': options.textLabels.toolbar.search,
                        }}
                        value={value || ''}
                        onChange={this.handleTextChange}
                        fullWidth={true}
                        inputRef={el => (this.searchField = el)}
                        placeholder={options.searchPlaceholder}
                    />
                    <IconButton className={classes.clearIcon} onClick={onHide}>
                        <ClearIcon/>
                    </IconButton>
                </div>
            </Grow>
        );
    }
}

export default withStyles(defaultSearchStyles, {name: 'MUIDataTableSearch'})(DebouncedTableSearch);
