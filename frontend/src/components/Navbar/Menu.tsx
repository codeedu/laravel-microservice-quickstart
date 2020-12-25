import React, { useState} from 'react';
import {IconButton, Menu as MuiMenu, MenuItem} from "@material-ui/core";
import MenuIcon from "@material-ui/icons/Menu";
import routes, {MyRoutesProps} from "../../routes";
import {Link} from 'react-router-dom'

const listRoutes = {
    'dashboard': 'Dashboard',
    'categoria.list': 'Categorias',
    'cast_members.list': 'Membros de Elenco',
    'genres.list': 'Gêneros'

}

const menuRoutes = routes.filter(route => Object.keys(listRoutes).includes(route.name));

export const Menu = () => {
    const [anchorEl, setAnchorEl] = useState(null);
    const open = Boolean(anchorEl);

    const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
    const handleClose = () => setAnchorEl(null);
    return (
        <>
            <IconButton
                edge="start"
                color="inherit"
                aria-controls="menu-appbar"
                aria-label="open drawer"
                aria-haspopup="true"
                onClick={handleOpen}
            >
                <MenuIcon/>
            </IconButton>
            <MuiMenu
                id="menu-appbar"
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose}
                anchorOrigin={{vertical:"bottom", horizontal:"center"}}
                transformOrigin={{vertical: "top", horizontal: "center"}}
                getContentAnchorEl={null}
            >
            {
                Object.keys(listRoutes).map(
                        (routeName,index) => {
                            // Coloquei esse as MyRoutesProps porque estava dando erro no Typescript. Isso é para ele
                            // saber o tipo da variavel
                            const route = menuRoutes.find(route => route.name === routeName) as MyRoutesProps;
                            return(
                                <MenuItem key={index} component={Link} to={route.path as string}>
                                    {listRoutes[routeName]}
                                </MenuItem>
                            )
                        }
                    )
                }
            </MuiMenu>
        </>
    );
};