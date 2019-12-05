import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import {httpVideo} from "../../util/http";
import {Chip} from "@material-ui/core";

import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from "../../util/http/category-http";
import {BadgeNo, BadgeYes} from "../../components/Badge";

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <BadgeYes/> : <BadgeNo/>;
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>
            }
        }
    }
];

interface Category {
    id: string;
    name: string;
}

type Props = {};
const Table = (props: Props) => {

    const [data, setData] = useState<Category[]>([]);
    //componentDidMount
    useEffect(() => {
        categoryHttp
            .list<{ data: Category[] }>() //{data: [], meta:}
            .then(({data}) => setData(data.data));
        // httpVideo.get('categories').then(
        //     response => setData(response.data.data)
        // )
    }, []);

    return (
        <MUIDataTable
            title=""
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;
