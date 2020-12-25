import React, {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import format from "date-fns/format"
import parseISO from "date-fns/parseISO"
import categoryHttp from "../../../util/http/category-http";
import {BadgeYes,BadgeNo} from '../../../components/Badge'

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue ){
                return value ? <BadgeYes/> : <BadgeNo/>
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

interface Category {
    id: string,
    name: string
}

const Table = () => {
    const [data,setData] = useState<Category[]>([])

    useEffect(() => {
        let isSubscribed = true;
        (async () =>{
            const {data} = await categoryHttp.list<{data: Category[]}>();
            if(isSubscribed){
                setData(data.data)
            }
        })();
        return () => {
            isSubscribed = false;
        }
    },[]);

    return (
        <MUIDataTable columns={columnsDefinition} data={data} title={'Listagem de Categorias'}/>
    );
};

export default Table;