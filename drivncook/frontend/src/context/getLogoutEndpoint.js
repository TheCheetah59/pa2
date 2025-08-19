export const getLogoutEndpoint = (user) =>
  user?.role === "client" ? "/api/customer/logout" : "/api/logout";

export default getLogoutEndpoint;
