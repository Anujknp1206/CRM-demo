import React, { memo } from "react";
import { motion } from "framer-motion";
import { Link } from "react-router-dom";
import { BASE_IMG_URL } from "../utils/config";

function Slider({ companyList = [] }) {
  const validCompanies = Array.isArray(companyList) ? companyList : [];

  if (validCompanies.length === 0) {
    return (
      <div className="text-sm text-gray-400 px-4 py-6">
        Loading companies...
      </div>
    );
  }

  // Determine how many repetitions are needed for smooth loop
  const MIN_DISPLAY_COUNT = 12;
  const repeatCount = Math.ceil(MIN_DISPLAY_COUNT / validCompanies.length);

  // Create repeated list
  const displayedCompanies = Array(repeatCount)
    .fill(validCompanies)
    .flat();

  return (
    <div className="flex items-center space-x-6 overflow-x-auto py-4 animate-slide">
  {[...displayedCompanies, ...displayedCompanies].map((company, index) => (
    <motion.div
      key={`${company?.name || "company"}-${index}`}
      className="flex-shrink-0 w-32 sm:w-36 bg-white rounded-xl shadow hover:shadow-md transition-all border border-gray-200"
      whileHover={{ scale: 1.05 }}
      transition={{ duration: 0.25 }}
    >
      <Link
        to={company?.slug ? `/company/${company.slug}` : "#"}
        className="flex flex-col items-center justify-center py-3"
      >
        <div className="w-20 h-20 sm:w-24 sm:h-24 mb-2 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50">
        <img
            src={`${BASE_IMG_URL}/company/logo/${company?.logo}`}
            alt={company?.name || "Company Logo"}
            loading="lazy"
            className="h-full w-full object-contain p-2 transition-transform duration-300 group-hover:scale-105"
        />
        </div>

        <span className="text-xs sm:text-sm text-gray-600 font-medium text-center group-hover:text-blue-600 transition-colors">
          {company?.name || "Unnamed"}
        </span>
      </Link>
    </motion.div>
  ))}
</div>

  );
}

export default memo(Slider);
