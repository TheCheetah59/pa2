import React, { useState } from "react";
import logoSite from "../../assets/image_logo_site.png";
import { useAuth } from "../../context/AuthContext";

// Composant Header avec Navigation
const Header = ({ t }) => {
  const { user, logout } = useAuth();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);

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

          <ul className={`nav-menu ${isMenuOpen ? "active" : ""}`}>
            <li>
              <a href="#home">{t.nav.home}</a>
            </li>
            <li>
              <a href="#services">{t.nav.services}</a>
            </li>
            <li>
              <a href="#menu">{t.nav.menu}</a>
            </li>
            <li>
              <a href="#contact">{t.nav.contact}</a>
            </li>
            <li>
              <button className="order-btn">{t.nav.order}</button>
            </li>
            {user ? (
              <>
                <li>
                  <a href="/profile">Profil</a>
                </li>
                <li>
                  <button onClick={logout}>Déconnexion</button>
                </li>
              </>
            ) : (
              <>
                <li>
                  <a href="/login">Connexion</a>
                </li>
                <li>
                  <a href="/register">Inscription</a>
                </li>
              </>
            )}
          </ul>

          <div
            className={`hamburger ${isMenuOpen ? "active" : ""}`}
            onClick={toggleMenu}
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

