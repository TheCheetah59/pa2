/* src/App.jsx */
import "../src/App.css";

function HeroSection() {
  return (
    <header className="bg-gray-800 text-white">
      <div className="max-w-7xl mx-auto flex flex-col-reverse md:flex-row items-center py-20 px-4">
        <div className="md:w-1/2 text-center md:text-left">
          <h1 className="text-5xl font-extrabold mb-6">
            Mangez mobile, mangez bon
          </h1>
          <button className="mt-4 bg-red-500 hover:bg-red-600 transition-transform transform hover:scale-105 py-3 px-8 rounded-full font-semibold">
            Voir nos menus
          </button>
        </div>
        <div className="md:w-1/2 flex justify-center">
          <img
            src="https://via.placeholder.com/600x400?text=Food+Truck"
            alt="Food truck"
            className="w-full rounded-lg shadow-lg mb-8 md:mb-0"
          />
        </div>
      </div>
    </header>
  );
}

function AboutSection() {
  return (
    <section className="bg-gray-100 py-16">
      <div className="max-w-4xl mx-auto px-4 text-center">
        <h2 className="text-3xl font-bold mb-4">Qui sommes-nous ?</h2>
        <p className="leading-relaxed text-gray-700">
          Créé en 2013 à Paris, Driv’n Cook réunit des food trucks offrant des
          plats faits maison à base de produits frais et locaux. Notre équipe de
          20 passionnés collabore avec de nombreux fournisseurs pour apporter le
          meilleur de la cuisine mobile partout en Île-de-France.
        </p>
      </div>
    </section>
  );
}

const services = [
  {
    title: "Food trucks",
    desc: "Des plats de qualité partout en ville",
    image: "./assets/image_food_truck.jpg",
  },
  {
    title: "Franchises",
    desc: "Rejoignez notre réseau en Île-de-France",
    image: "https://source.unsplash.com/400x300/?franchise",
  },
  {
    title: "Carte de fidélité",
    desc: "Cumulez des points à chaque commande",
    image: "https://source.unsplash.com/400x300/?loyalty",
  },
  {
    title: "Commandes en ligne",
    desc: "Commandez depuis notre site ou application",
    image: "https://source.unsplash.com/400x300/?online,order",
  },
  {
    title: "Événements",
    desc: "Animations et dégustations régulières",
    image: "https://source.unsplash.com/400x300/?event,food",
  },
];


function ServicesSection() {
  return (
    <section className="py-16">
      <div className="max-w-6xl mx-auto px-4 text-center">
        <h2 className="text-3xl font-bold mb-8">Nos services</h2>
        <div className="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
          {services.map((srv) => (
            <div
              key={srv.title}
              className="bg-white p-6 rounded-lg shadow-md transition-transform hover:scale-105"
            >
              <div className="text-4xl mb-3">{srv.emoji}</div>
              <h3 className="font-semibold mb-1">{srv.title}</h3>
              <p className="text-sm text-gray-600">{srv.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

const reasons = [
  { emoji: "🥦", text: "Produits frais et locaux" },
  { emoji: "⚡", text: "Service rapide" },
  { emoji: "🎁", text: "Récompenses fidélité" },
  { emoji: "📱", text: "Commande en ligne pratique" },
];

function WhyChooseUsSection() {
  return (
    <section className="bg-gray-50 py-16">
      <div className="max-w-4xl mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-6">
          Pourquoi nous choisir ?
        </h2>
        <ul className="space-y-4">
          {reasons.map((r) => (
            <li key={r.text} className="flex items-start space-x-3">
              <span className="text-2xl">{r.emoji}</span>
              <span className="text-gray-700">{r.text}</span>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}

function Footer() {
  return (
    <footer className="bg-gray-800 text-white py-8">
      <div className="max-w-4xl mx-auto px-4 text-center space-y-4">
        <p>contact@drivncook.fr | 01&nbsp;23&nbsp;45&nbsp;67&nbsp;89</p>
        <div className="flex justify-center space-x-6 text-2xl">
          <a href="#" className="hover:scale-110 transition-transform">
            🐦
          </a>
          <a href="#" className="hover:scale-110 transition-transform">
            📘
          </a>
          <a href="#" className="hover:scale-110 transition-transform">
            📷
          </a>
        </div>
        <p className="text-sm text-gray-400">© 2013‑2024 Driv’n Cook</p>
      </div>
    </footer>
  );
}

export default function App() {
  return (
    <div className="font-sans text-gray-800">
      <HeroSection />
      <AboutSection />
      <ServicesSection />
      <WhyChooseUsSection />
      <Footer />
    </div>
  );
}
