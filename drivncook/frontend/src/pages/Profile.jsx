import { useEffect, useState } from "react";
import api from "../axios.jsx";

const Profile = () => {
  const [profile, setProfile] = useState(null);

  useEffect(() => {
    api
      .get("/api/customer/profile")
      .then(({ data }) => setProfile(data))
      .catch((err) => console.error(err));
  }, []);

  if (!profile) {
    return <p>Chargement...</p>;
  }

  return (
    <div className="p-4">
      <h1>Profil</h1>
      <p>Nom : {profile.name}</p>
      <p>Email : {profile.email}</p>
    </div>
  );
};

export default Profile;
