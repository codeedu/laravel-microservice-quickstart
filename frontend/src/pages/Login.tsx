// @flow
import { useKeycloak } from "@react-keycloak/web";
import * as React from "react";
import { Redirect, useLocation } from "react-router-dom";
import Waiting from "../components/Waiting";

interface LoginProps {}

const Login: React.FC<LoginProps> = (props) => {
  const { keycloak } = useKeycloak();
  const location = useLocation();

  const { from } = location.state || { from: { pathname: "/" } };
  
  if (keycloak!.authenticated === true) {
    return <Redirect to={from} />;
  }

  keycloak!.login({
      redirectUri: `${window.location.origin}${process.env.REACT_APP_BASENAME}${from.pathname}`
  });

  return <Waiting/>;
};

export default Login;
