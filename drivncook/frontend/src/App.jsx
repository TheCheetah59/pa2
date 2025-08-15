import React from "react";
import AppRoutes from "./Routes"; // Assure-toi que c’est bien le bon nom
import Header from "./components/accueil/Header";

const App = () => (
  <>
    <Header />
    <AppRoutes />
  </>
);

export default App;
