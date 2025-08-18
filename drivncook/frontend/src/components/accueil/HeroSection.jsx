import React from "react";
import { useTranslation } from "react-i18next";
import logoSite from "../../assets/image_logo_site.png"; // ← Import du logo

// Composant Hero Section
const HeroSection = () => {
  const { t } = useTranslation();
  return (
    <section id="home" className="hero">
      <div className="hero-container">
        <div className="hero-content">
          <h1 className="hero-title">{t("hero.title")}</h1>
          <p className="hero-subtitle">{t("hero.subtitle")}</p>
          <button className="cta-button">{t("hero.cta")}</button>
        </div>
        <div className="hero-image">
          <div className="logo-container">
            <img
              src={logoSite}
              alt="Driv'n Cook Logo"
              className="hero-logo"
              onError={(e) => {
                e.target.style.display = "none";
                e.target.nextElementSibling.style.display = "block";
              }}
            />
            <div className="food-truck-placeholder" style={{ display: "none" }}>
              🚚
            </div>
            🚚
          </div>
        </div>
      </div>
    </section>
  );
};

export default HeroSection;
