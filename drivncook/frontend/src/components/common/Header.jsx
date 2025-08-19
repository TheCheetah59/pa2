import React, { useState } from "react";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";
import logoSite from "../../assets/image_logo_site.png";
import { useAuth } from "../../context/AuthContext.jsx";

// Header component with navigation, auth and language switcher
const Header = () => {
  const { t, i18n } = useTranslation();
  const { user, logout } = useAuth();
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const toggleMenu = () => setIsMenuOpen((v) => !v);

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
                e.currentTarget.style.display = "none";
                e.currentTarget.nextElementSibling.style.display = "block";
              }}
            />
            <h2 style={{ display: "none" }}>DRIV'N COOK</h2>
          </div>

          <ul className={`nav-menu ${isMenuOpen ? "active" : ""}`}>
            <li>
              <a href="#home">{t("nav.home")}</a>
            </li>
            <li>
              <a href="#services">{t("nav.services")}</a>
            </li>
            <li>
              <Link to="/menu">{t("nav.menu")}</Link>
            </li>
            <li>
              <Link to="/contact">{t("nav.contact")}</Link>
            </li>
            {user ? (
              <>
                <li>
                  <a href="/profile">{t("nav.profile")}</a>
                </li>
                <li>
                  <button onClick={logout}>{t("nav.logout")}</button>
                </li>
              </>
            ) : (
              <>
                <li>
                  <a href="/login">{t("nav.login")}</a>
                </li>
                <li>
                  <a href="/register">{t("nav.register")}</a>
                </li>
              </>
            )}
          </ul>
          <div className="lang-switcher">
            <button onClick={() => i18n.changeLanguage("fr")}>fr</button>
            <button onClick={() => i18n.changeLanguage("en")}>en</button>
          </div>

          <button
            className={`hamburger ${isMenuOpen ? "active" : ""}`}
            onClick={toggleMenu}
            aria-label="Toggle menu"
            aria-expanded={isMenuOpen}
          >
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
      </nav>
    </header>
  );
};

export default Header;

