import React from "react";
import "./styles/Accueil.css";

// Import des composants
import Header from "../components/accueil/Header";
import AboutSection from "../components/accueil/AboutSection";
import HeroSection from "../components/accueil/HeroSection"; // ← Import du composant HeroSection
import ServicesSection from "../components/accueil/ServicesSection"; // ← Import du composant ServicesSection
import WhyChooseUs from "../components/accueil/WhyChooseUs";
import Footer from "../components/accueil/Footer"; // ← Import du composant Footer



// Composant principal App
const Accueil = () => {
  return (
    <div className="App">
      <Header />
      <HeroSection />
      <ServicesSection />
      <AboutSection />
      <WhyChooseUs />
      <Footer />
      <Footer t={t} /> {/* ← Passer t en props */}
    </div>
  );
};

export default Accueil;
