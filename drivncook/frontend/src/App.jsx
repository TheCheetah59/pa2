import React from "react";
import { BrowserRouter } from "react-router-dom";
import Routes from "./Routes";
import { AuthProvider } from "./useAuth";

const App = () => (
  <AuthProvider>
    <BrowserRouter>
      <Routes />
    </BrowserRouter>
  </AuthProvider>
);
export default App;
