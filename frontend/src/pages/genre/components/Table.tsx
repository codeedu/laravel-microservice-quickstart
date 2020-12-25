import React, {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import httpVideo from "../../../util/http";
import format from "date-fns/format"
import parseISO from "date-fns/parseISO"
import categoryHttp from "../../../util/http/category-http";
import genreHttp from "../../../util/http/genre-http";


const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "categories",
        label: "Categorias",
        options: {
            customBodyRender(value, tableMeta, updateValue ){
                return value.map(value => value.name).join(', ')
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue ){
                return <span>{format(parseISO(value),'dd/MM/yyyy')}</span>
            }
        }
    }
];

type Props = {};
const Table = (props: Props) => {
    const [data,setData] = useState([])

    useEffect(() => {
        let isSubscribed = true;
        (async () =>{
            const {data} = await genreHttp.list();
            if(isSubscribed){
                setData(data.data)
            }
        })();
        return () => {
            isSubscribed = false;
        }
    },[]);
    return (
        <MUIDataTable columns={columnsDefinition} data={data} title={'Listagem de Generos'}/>
    );
};

export default Table;