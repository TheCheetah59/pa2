import React, { useState } from "react"; // ← Ajout de useState
import logoSite from "../../assets/image_logo_site.png";

const Header = ({ t }) => {
  // ← Ajout des props t
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
              <a href="#home">{t ? t.nav.home : "Accueil"}</a>
            </li>
            <li>
              <a href="#services">{t ? t.nav.services : "Services"}</a>
            </li>
            <li>
              <a href="#menu">{t ? t.nav.menu : "Menu"}</a>
            </li>
            <li>
              <a href="#contact">{t ? t.nav.contact : "Contact"}</a>
            </li>
            <li>
              <button className="order-btn">
                {t ? t.nav.order : "Commander"}
              </button>
            </li>
          </ul>

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
