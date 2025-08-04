// Composant Header avec Navigation
const Header = () => (
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
        <ul className="nav-menu">
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
        </ul>
        <div className="hamburger">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </nav>
  </header>
);
