import { useKeycloak } from "@react-keycloak/web";
import { useMemo } from "react";

export function useHasRealmRole(role: string) {
  const { keycloak, initialized } = useKeycloak();

  const hasRole = useMemo(() => {
    if (!initialized || !keycloak.authenticated) {
      return false;
    }
    return keycloak.hasRealmRole(role);
  }, [initialized, keycloak, role]);

  return hasRole;
}

export function useHasClient(clientName: string) {
  const { keycloak, initialized } = useKeycloak();

  const hasRole = useMemo(() => {
    if (!initialized || !keycloak.authenticated) {
      return false;
    }
    const countRoles = keycloak.resourceAccess?.[clientName]?.roles?.length;
    return !countRoles ? false : true;
  }, [initialized, keycloak, clientName]);

  return hasRole;
}
