import React from "react";

// Composant Footer
const Footer = ({ t }) => (
  <footer className="footer">
    <div className="container">
      <div className="footer-content">
        <div className="footer-info">
          <h3>DRIV'N COOK</h3>
          <p>{t.footer.email}</p>
        </div>
        <div className="footer-social">
          <h4>{t.footer.followUs}</h4>
          <div className="social-links">
            <span>📘</span>
            <span>📷</span>
            <span>🐦</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
);

export default Footer;