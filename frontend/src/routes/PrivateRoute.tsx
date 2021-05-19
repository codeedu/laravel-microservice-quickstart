// @flow
import { useKeycloak } from "@react-keycloak/web";
import * as React from "react";
import {
  Redirect,
  Route,
  RouteComponentProps,
  RouteProps,
} from "react-router-dom";
import { useHasRealmRole } from "../hooks/useHasRole";
import { NotAuthorized } from "../pages/NotAuthorized";
interface PrivateProps extends RouteProps {
  component:
    | React.ComponentType<RouteComponentProps<any>>
    | React.ComponentType<any>;
}
const PrivateRoute: React.FC<PrivateProps> = (props) => {
  const { component: Component, ...rest } = props;
  const { keycloak } = useKeycloak();
  const hasCatalogAdmin = useHasRealmRole('catalog-admin');
  const render = React.useCallback((props) => {
    if (keycloak.authenticated) {
      console.log(keycloak);
      return hasCatalogAdmin ? <Component {...props} />: <NotAuthorized/>;
    }

    return (
      <Redirect
        to={{
          pathname: "/login",
          state: {from: props.location}
        }}
      />
    );
  }, [hasCatalogAdmin]);
  return <Route {...rest} render={render} />;
};

export default PrivateRoute;