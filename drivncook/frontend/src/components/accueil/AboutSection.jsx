import React from "react";
import { useTranslation } from "react-i18next";

// Composant About Section
const AboutSection = () => {
  const { t } = useTranslation();
  return (
    <section className="about">
      <div className="container">
        <h2 className="section-title">{t("about.title")}</h2>
        <p className="about-text">{t("about.description")}</p>
      </div>
    </section>
  );
};

export default AboutSection;
