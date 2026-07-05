import React, { memo } from "react";
import { Link } from "react-router-dom";
import { useSelector } from "react-redux";
import { BASE_IMG_URL } from "../utils/config";

/**
 * Logo component that displays the site logo from Redux store
 * Falls back to default logo if the main logo fails to load
 */
const Logo = () => {
  const { settingsData, error } = useSelector((state) => state.settingsData);

  if (error) {
    console.error("Error loading logo:", error);
    return <div className="text-red-500">Error loading logo</div>;
  }

  const logoPath = settingsData?.settings?.[0]?.logo;
  const logoUrl = logoPath ? `${BASE_IMG_URL}logo/${logoPath}` : "./default-logo.jpg";

  return (
    <div className="cursor-pointer">
      <Link to="/">
        <img
          className="max-h-16"
          src={logoUrl}
          alt="Site logo"
          onError={(e) => {
            e.target.onerror = null;
            e.target.src = "./default-logo.jpg";
          }}
        />
      </Link>
    </div>
  );
};

export default memo(Logo);