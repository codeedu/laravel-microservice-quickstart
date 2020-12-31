import {useReducer, useState} from "react";
import reducer, {INITIAL_STATE} from "../store/filter";

export default function useFilter(){
    const [filterState, dispatch] = useReducer(reducer,INITIAL_STATE);
    const [totalRecords, setTotalRecords] = useState<number>(0);

    return{
        filterState,
        dispatch,
        totalRecords,
        setTotalRecords
    }
}