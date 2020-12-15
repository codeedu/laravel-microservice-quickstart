import {RouteProps} from 'react-router-dom';
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/CategoryList";

export interface MyRoutesProps extends RouteProps {
    name: string,
    label: string
}
const routes: MyRoutesProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        name: 'categoria.list',
        label: 'Lista de Categorias',
        path: '/categorias',
        component: CategoryList,
        exact: true
    }
]


export default routes;