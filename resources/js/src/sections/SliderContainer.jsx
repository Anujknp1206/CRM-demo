import React, { memo } from "react";
import { motion } from "framer-motion";
import { Slider } from "../components";
import { useFetchThunk } from "../hooks/useFetchThunk";
import { fetchAllCompanies } from "../features/functions";
import { useSelector } from "react-redux";

const fetchConfigs = [{ thunk: fetchAllCompanies }];

function SliderContainer({ heading = "", para = "" }) {
  useFetchThunk(fetchConfigs);

  const companyList = useSelector(
    (state) => state.companies.allCompanies?.companies || []
  );

  return (
    <section className="w-screen bg-gray-50 py-20 relative left-1/2 right-1/2 -mx-[50vw]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {/* Heading Section */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
        >
          <h2 className="text-2xl sm:text-3xl font-semibold text-gray-800 tracking-wide uppercase">
            {heading}
          </h2>
          {para && (
            <p className="mt-2 text-sm sm:text-base text-gray-500 max-w-xl mx-auto">
              {para}
            </p>
          )}
        </motion.div>

        {/* Slider Section */}
        <div className="relative overflow-hidden py-8">
          {/* Left & Right fade gradients */}
          <div className="absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-gray-50 to-transparent z-10"></div>
          <div className="absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-gray-50 to-transparent z-10"></div>

          {/* Slider component (custom style passed) */}
          <Slider
            companyList={companyList}
            iconClass="h-16 sm:h-20 object-contain" // 👈 Bigger logo/icon
            titleClass="text-xs sm:text-sm text-center text-gray-600 mt-2" // 👈 Smaller title
          />
        </div>
      </div>
    </section>
  );
}

export default memo(SliderContainer);
