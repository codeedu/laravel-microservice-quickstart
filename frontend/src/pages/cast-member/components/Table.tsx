import React, {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import httpVideo from "../../../util/http";
import format from "date-fns/format"
import parseISO from "date-fns/parseISO"
import categoryHttp from "../../../util/http/category-http";

const CastMembersTypeMap = {
    1: 'Diretor',
    2: 'Ator'
}

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "type",
        label: "Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue ){
                return CastMembersTypeMap[value]
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


const Table = () => {
    const [data,setData] = useState([])

    useEffect(() => {
        let isSubscribed = true;
        (async () =>{
            const {data} = await httpVideo.get('cast-members');
            if(isSubscribed){
                setData(data.data)
            }
        })();
        return () => {
            isSubscribed = false;
        }
    },[]);
    return (
        <MUIDataTable columns={columnsDefinition} data={data} title={'Listagem de Elenco'}/>
    );
};

export default Table;