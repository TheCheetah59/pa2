import React from "react";
import { useTranslation } from "react-i18next";
// Composant Why Choose Us
const WhyChooseUs = () => {
  const { t } = useTranslation();
  return (
    <section className="why-us">
      <div className="container">
        <h2 className="section-title">{t("whyUs.title")}</h2>
        <div className="advantages-grid">
          <div className="advantage">{t("whyUs.fresh")}</div>
          <div className="advantage">{t("whyUs.quality")}</div>
          <div className="advantage">{t("whyUs.mobile")}</div>
          <div className="advantage">{t("whyUs.experience")}</div>
        </div>
      </div>
    </section>
  );
};

export default WhyChooseUs;
