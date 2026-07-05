import React, { memo } from "react";
import { motion } from "framer-motion";
import { fadeUpVariants, staggerContainer } from "../utils/animations";
import { featuredVariantsMap } from "./featuredGroup/FeatureBlogsVariants";

function FeatureBlogs({ category, handleClick }) {
  const { blogs = [], name, id } = category;
  const VariantComponent = featuredVariantsMap[blogs.length] || featuredVariantsMap.default;

  return (
    <motion.section
      className="w-full font-sans"
      variants={staggerContainer}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, amount: 0.1 }}
    >
      {/* Header */}
      <motion.header
        className="mb-6 flex items-center justify-between border-b pb-4"
        variants={fadeUpVariants}
      >
        <h1 className="text-2xl md:text-3xl font-bold text-gray-800 uppercase tracking-wide">
          {name}
        </h1>
        <button
          onClick={() => handleClick(id)}
          className="text-sm text-blue-600 hover:text-white font-medium px-3 py-1 rounded hover:bg-blue-600 transition-colors border border-blue-600"
        >
          Show All
        </button>
      </motion.header>

      {/* Content */}
      <motion.div
        className="bg-white p-6"
        variants={fadeUpVariants}
      >
        <VariantComponent blogs={blogs} handleClick={handleClick} />
      </motion.div>
    </motion.section>
  );
}

export default memo(FeatureBlogs);
