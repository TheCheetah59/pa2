import React, { useState } from "react"; // ← Ajout de useState
import { useTranslation } from "react-i18next";
import logoSite from "../../assets/image_logo_site.png";

const Header = () => {
  const { t, i18n } = useTranslation();
  const [isMenuOpen, setIsMenuOpen] = useState(false); // ← État du menu

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen); // ← Fonction toggle
  };

  return (
    <header className="header">
      <nav className="navbar">
        <div className="nav-container">
          <div className="nav-logo">
            <img
              src={logoSite}
              alt="Driv'n Cook"
              className="nav-logo-img"
              onError={(e) => {
                e.target.style.display = "none";
                e.target.nextElementSibling.style.display = "block";
              }}
            />
            <h2 style={{ display: "none" }}>DRIV'N COOK</h2>
          </div>

          {/* Menu avec classe dynamique */}
          <ul className={`nav-menu ${isMenuOpen ? "active" : ""}`}>
            <li>
              <a href="#home">{t("home")}</a>
            </li>
            <li>
              <a href="#services">{t("services")}</a>
            </li>
            <li>
              <a href="#menu">{t("menu")}</a>
            </li>
            <li>
              <a href="#contact">{t("contact")}</a>
            </li>
            <li>
              <button className="login-btn">
                <a href="/login">{t("login")}</a>
              </button>
            </li>
          </ul>
          <div className="lang-switcher">
            <button onClick={() => i18n.changeLanguage("fr")}>fr</button>
            <button onClick={() => i18n.changeLanguage("en")}>en</button>
          </div>

          {/* Hamburger avec événement click et animation */}
          <div
            className={`hamburger ${isMenuOpen ? "active" : ""}`}
            onClick={toggleMenu} // ← Événement click
          >
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </nav>
    </header>
  );
};

export default Header;
