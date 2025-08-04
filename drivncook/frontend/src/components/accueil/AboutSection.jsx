import React from "react";

// Composant About Section
const AboutSection = ({ t }) => (
  <section className="about">
    <div className="container">
      <h2 className="section-title">{t.about.title}</h2>
      <p className="about-text">{t.about.description}</p>
    </div>
  </section>
);

export default AboutSection;
