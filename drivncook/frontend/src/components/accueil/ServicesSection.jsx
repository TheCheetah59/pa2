import React from "react";

// Composant Service Card (AJOUTÉ)
const ServiceCard = ({ icon, title, description }) => (
  <div className="service-card">
    <div className="service-icon">{icon}</div>
    <h3 className="service-title">{title}</h3>
    <p className="service-description">{description}</p>
  </div>
);

// Composant Services Section
const ServicesSection = (
  { t } // ← Format correct des props
) => (
  <section id="services" className="services">
    <div className="container">
      <h2 className="section-title">{t.services.title}</h2>
      <div className="services-grid">
        <ServiceCard
          icon="🚚"
          title={t.services.foodtruck.title}
          description={t.services.foodtruck.description}
        />
        <ServiceCard
          icon="🤝"
          title={t.services.franchise.title}
          description={t.services.franchise.description}
        />
        <ServiceCard
          icon="💳"
          title={t.services.loyalty.title}
          description={t.services.loyalty.description}
        />
        <ServiceCard
          icon="📱"
          title={t.services.online.title}
          description={t.services.online.description}
        />
        <ServiceCard
          icon="🎉"
          title={t.services.events.title}
          description={t.services.events.description}
        />
        <ServiceCard
          icon="📚"
          title={t.services.courses.title}
          description={t.services.courses.description}
        />
      </div>
    </div>
  </section>
);

export default ServicesSection;
