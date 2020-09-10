import {
  Dispatch,
  Reducer,
  useEffect,
  useReducer,
  useState,
  useMemo,
  useCallback,
} from "react";
import reducer, { Creators } from "../store/filter";
import {
  Actions as FilterActions,
  State as FilterState,
} from "../store/filter/types";
import { MUIDataTableColumn } from "mui-datatables";
import { useDebounce } from "use-debounce";
import { useHistory, useLocation } from "react-router";
import { History } from "history";
import { isEqual } from "lodash";
import * as yup from "../util/vendor/yup";
import { MuiDataTableRefComponent } from "../components/Table";
import { ObjectSchema } from "../util/vendor/yup";

interface FilterManagerOptions {
  schema: ObjectSchema;
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  tableRef: React.MutableRefObject<MuiDataTableRefComponent>;
  dispatch: Dispatch<FilterActions>;
  state: FilterState;
}

interface ExtraFilter {
  getStateFromURL: (queryParams: URLSearchParams) => any;
  formatSearchParams: (debouncedState: FilterState) => any;
  createValidationSchema: () => any;
}

interface UseFilterOptions {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime: number;
  tableRef: React.MutableRefObject<MuiDataTableRefComponent>;
  extraFilter?: ExtraFilter;
}
let count = 0;
export default function useFilter(options: UseFilterOptions) {
  console.log(++count);
  const history = useHistory();
  const location = useLocation();
  const {
    search: locationSearch,
    pathname: locationPathname,
    state: locationState,
  } = location;
  const { rowsPerPage, rowsPerPageOptions, extraFilter, columns } = options;

  const schema = useMemo(() => {
    return yup.object().shape<FilterState>({
      search: yup
        .string()
        .transform((value) => (!value ? undefined : value))
        .default(""),
      pagination: yup.object().shape({
        page: yup
          .number()
          .transform((value) =>
            isNaN(value) || parseInt(value) < 1 ? undefined : value
          )
          .default(1),
        per_page: yup
          .number()
          .transform((value) =>
            isNaN(value) || !rowsPerPageOptions.includes(parseInt(value))
              ? undefined
              : value
          )
          .default(rowsPerPage),
      }),
      order: yup.object().shape({
        sort: yup
          .string()
          .nullable()
          .transform((value) => {
            const columnsName = columns
              .filter(
                (column) => !column.options || column.options.sort !== false
              )
              .map((column) => column.name);
            return columnsName.includes(value) ? value : undefined;
          })
          .default(null),
        dir: yup
          .string()
          .nullable()
          .transform((value) =>
            !value || !["asc", "desc"].includes(value.toLowerCase())
              ? undefined
              : value
          )
          .default(null),
      }),
      ...(extraFilter && {
        extraFilter: extraFilter.createValidationSchema(),
      }),
    });
  }, [rowsPerPageOptions, rowsPerPage, columns, extraFilter]);

  //react-router history mutable | location immutable
  const stateFromUrl = useMemo<FilterState>(() => {
    const queryParams = new URLSearchParams(locationSearch.substr(1));
    return schema.cast({
      search: queryParams.get("search"),
      pagination: {
        page: queryParams.get("page"),
        per_page: queryParams.get("per_page"),
      },
      order: {
        sort: queryParams.get("sort"),
        dir: queryParams.get("dir"),
      },
      ...(extraFilter && {
        extraFilter: extraFilter.getStateFromURL(queryParams),
      }),
    });
  }, [locationSearch, schema, extraFilter]);

  const cleanSearchText = useCallback((text) => {
    let newText = text;
    if (text && text.value !== undefined) {
      newText = text.value;
    }
    return newText;
  }, []);

  const formatSearchParams = useCallback(
    (state, extraFilter) => {
      const search = cleanSearchText(state.search);
      return {
        ...(search && search !== "" && { search: search }),
        ...(state.pagination.page !== 1 && { page: state.pagination.page }),
        ...(state.pagination.per_page !== 15 && {
          per_page: state.pagination.per_page,
        }),
        ...(state.order.sort && {
          sort: state.order.sort,
          dir: state.order.dir,
        }),
        ...(extraFilter && extraFilter.formatSearchParams(state)),
      };
    },
    [cleanSearchText]
  );

  const INITIAL_STATE = stateFromUrl;
  const [filterState, dispatch] = useReducer<
    Reducer<FilterState, FilterActions>
  >(reducer, INITIAL_STATE);
  const filterManager = new FilterManager({ ...options, state: filterState, dispatch, schema });
  const [debouncedFilterState] = useDebounce(filterState, options.debounceTime);
  const [totalRecords, setTotalRecords] = useState<number>(0);

  useEffect(() => {
    console.log("asdfasdf");
    history.replace({
      pathname: locationPathname,
      search:
        "?" +
        new URLSearchParams(formatSearchParams(stateFromUrl, extraFilter)),
      state: stateFromUrl,
    });
  }, [
    history,
    locationPathname,
    formatSearchParams,
    stateFromUrl,
    extraFilter,
  ]);

  useEffect(() => {
    const newLocation = {
      pathname: locationPathname,
      search:
        "?" +
        new URLSearchParams(
          formatSearchParams(debouncedFilterState, extraFilter)
        ),
      state: {
        ...debouncedFilterState,
        search: cleanSearchText(debouncedFilterState.search),
      },
    };

    const oldState = locationState;
    const nextState = debouncedFilterState;

    if (isEqual(oldState, nextState)) {
      return;
    }

    history.push(newLocation);
  }, [
    history,
    locationPathname,
    formatSearchParams,
    locationState,
    cleanSearchText,
    debouncedFilterState,
    extraFilter,
  ]);

  filterManager.state = filterState;

  filterManager.applyOrderInColumns();

  return {
    columns: filterManager.columns,
    cleanSearchText,
    filterManager,
    filterState,
    debouncedFilterState,
    dispatch,
    totalRecords,
    setTotalRecords,
  };
}

export class FilterManager {
  schema;
  state: FilterState;
  dispatch: Dispatch<FilterActions>;
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  tableRef: React.MutableRefObject<MuiDataTableRefComponent>;

  constructor(options: FilterManagerOptions) {
    const {
      schema,
      columns,
      rowsPerPage,
      tableRef,
      dispatch,
      state
    } = options;
    this.schema = schema;
    this.columns = columns;
    this.rowsPerPage = rowsPerPage;
    this.tableRef = tableRef;
    this.dispatch = dispatch;
    this.state = state;
  }

  private resetTablePagination() {
    this.tableRef.current.changeRowsPerPage(this.rowsPerPage);
    this.tableRef.current.changePage(0);
  }

  changeSearch(value) {
    this.dispatch(Creators.setSearch({ search: value }));
  }

  changePage(page) {
    this.dispatch(Creators.setPage({ page: page + 1 }));
  }

  changeRowsPerPage(perPage) {
    this.dispatch(Creators.setPerPage({ per_page: perPage }));
  }

  changeColumnSort(changedColumn: string, direction: string) {
    this.dispatch(
      Creators.setOrder({
        sort: changedColumn,
        dir: direction.includes("desc") ? "desc" : "asc",
      })
    );
    this.resetTablePagination();
  }

  changeExtraFilter(data) {
    //{type: 'Diretor'}
    this.dispatch(Creators.updateExtraFilter(data));
  }

  resetFilter() {
    const INITIAL_STATE = {
      ...this.schema.cast({}),
      search: { value: null, update: true },
    };
    this.dispatch(
      Creators.setReset({
        state: INITIAL_STATE,
      })
    );
    this.resetTablePagination();
  }

  applyOrderInColumns() {
    this.columns = this.columns.map((column) => {
      return column.name === this.state.order.sort
        ? {
            ...column,
            options: {
              ...column.options,
              sortDirection: this.state.order.dir as any,
            },
          }
        : column;
    });
  }
}
