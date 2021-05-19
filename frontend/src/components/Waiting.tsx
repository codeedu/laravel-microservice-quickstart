// @flow
import { Container, Typography } from "@material-ui/core";
import * as React from "react";
interface WaitingProps {}
const Waiting: React.FunctionComponent<WaitingProps> = (props) => {
  return (
    <Container>
      <Typography>Aguarde...</Typography>
    </Container>
  );
};

export default Waiting;