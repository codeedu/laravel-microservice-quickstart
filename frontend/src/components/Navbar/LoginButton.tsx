// @flow
import { Button } from "@material-ui/core";
import { useKeycloak } from "@react-keycloak/web";
import * as React from "react";
interface LoginButtonProps {}
const LoginButton: React.FunctionComponent<LoginButtonProps> = (props) => {
  const { keycloak, initialized } = useKeycloak();

  if (!initialized || keycloak.authenticated) {
    return null;
  }

  return <Button color="inherit">Login</Button>;
};

export default LoginButton;
