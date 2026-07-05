import React, { memo } from "react";
import { motion } from "framer-motion";
import { useCategories } from "../hooks/useCategories";

const colorVariants = [
  "bg-gradient-to-r from-purple-400 to-pink-400",
  "bg-gradient-to-r from-blue-400 to-cyan-400",
  "bg-gradient-to-r from-orange-400 to-amber-400",
  "bg-gradient-to-r from-green-400 to-emerald-400",
  "bg-gradient-to-r from-red-400 to-rose-400",
  "bg-gradient-to-r from-indigo-400 to-violet-400",
];

function FeaturePills({ heading = "", para = "" }) {

  const [ categories, handleClick ] = useCategories();

  return (
    <div className="py-16 px-6 sm:px-12 bg-white">
      <div className="max-w-6xl mx-auto">

        <motion.div
          className="text-center mb-12"
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
        >
          <h2 className="text-3xl sm:text-4xl font-bold mb-4 text-gray-900">{heading}</h2>
          <p className="text-gray-600 max-w-2xl mx-auto">{para}</p>
        </motion.div>

        <div className="flex flex-wrap justify-center gap-3 sm:gap-4">
          {categories.map((category, index) => {
            const colorClass = colorVariants[index % colorVariants.length];

            return (
              <motion.div
                key={category.id}
                initial={{ opacity: 0, scale: 0.8 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: index * 0.1 }}
                whileHover={{ y: -4 }}
              >
                <button className={`${colorClass} cursor-pointer text-white px-6 py-3 rounded-full font-medium flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105 select-none `}
                  onClick={() => handleClick(category.id.toString())}
                >
                  <span className="font-semibold">{category.name}</span>
                </button>
              </motion.div>
            );
          })}
        </div>
      </div>
    </div>
  );
}

export default memo(FeaturePills);
