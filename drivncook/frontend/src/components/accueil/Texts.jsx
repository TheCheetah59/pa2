import React from "react";

// Configuration des textes (préparé pour i18n)
const texts = {
  fr: {
    nav: {
      home: "Accueil",
      services: "Services",
      menu: "Menu",
      contact: "Contact",
      order: "Commander",
    },
    hero: {
      title: "Mangez mobile, mangez bon",
      subtitle:
        "Des plats de qualité préparés avec des produits frais et locaux, directement dans nos food trucks",
      cta: "Découvrir notre menu",
    },
    services: {
      title: "Nos Services",
      foodtruck: {
        title: "Food Trucks",
        description:
          "Des camions équipés dans toute l'Île-de-France pour vous servir des plats frais",
      },
      franchise: {
        title: "Franchises",
        description:
          "Rejoignez notre réseau et devenez entrepreneur avec notre concept éprouvé",
      },
      loyalty: {
        title: "Carte de Fidélité",
        description:
          "Profitez d'avantages exclusifs, réductions et invitations à nos événements",
      },
      online: {
        title: "Commande en Ligne",
        description:
          "Commandez et réservez vos plats préférés directement depuis notre site",
      },
      events: {
        title: "Événements",
        description:
          "Dégustations, animations culinaires et événements spéciaux toute l'année",
      },
      courses: {
        title: "Cours de Cuisine",
        description:
          "Accédez à des cours personnalisés chaque mois grâce à notre abonnement en ligne",
      },
    },
    whyUs: {
      title: "Pourquoi nous choisir ?",
      fresh: "🌱 Produits frais et locaux",
      quality: "⭐ Qualité garantie",
      mobile: "🚚 Service mobile",
      experience: "👨‍🍳 Savoir-faire artisanal",
    },
    about: {
      title: "Qui sommes-nous ?",
      description:
        "Créée en 2013 dans le 12ème arrondissement de Paris, Driv'n Cook est devenue une référence dans le secteur des food trucks. Notre concept unique allie qualité culinaire et mobilité, avec des produits frais, bruts et majoritairement locaux. Avec plus de 30 franchisés, nous continuons notre expansion en Île-de-France.",
    },
    footer: {
      email: "contact@drivncook.fr",
      followUs: "Suivez-nous",
    },
  },
};

const currentLang = "fr";
const t = texts[currentLang];


export { texts, t, currentLang };