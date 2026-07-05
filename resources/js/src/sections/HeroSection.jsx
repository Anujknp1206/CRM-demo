import React, { memo } from "react";
import { motion } from "framer-motion";
import { fadeUpVariants, staggerContainer } from "../utils/animations";

function HeroSection({ heading = "", para = "", children = null }) {
  return (
    <section className="relative w-full h-screen/2 py-8 pt-12 max-h-[800px] overflow-hidden">
      {/* Gradient background */}
      <div className="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-cyan-500 animate-gradient-shift"></div>

      {/* Floating background elements */}
      <FloatingBackground />

      {/* Content */}
      <motion.div
        className="relative z-10 h-full flex flex-col justify-center items-center px-6 text-center text-white"
        initial="hidden"
        animate="visible"
        variants={staggerContainer}
      >
        <motion.h1
          className="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight"
          variants={fadeUpVariants}
        >
          {heading}
        </motion.h1>

        <motion.p
          className="text-lg md:text-xl max-w-2xl mb-10 opacity-90"
          variants={fadeUpVariants}
        >
          {para}
        </motion.p>

        <motion.div
          className="flex flex-col sm:flex-row gap-4 w-full max-w-md"
          variants={fadeUpVariants}
        >
          {children}
        </motion.div>
      </motion.div>
    </section>
  );
}

export default memo(HeroSection);

const FloatingBackground = () => (
  <div className="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20">
    <div className="absolute -top-20 -left-20 w-64 h-64 rounded-full bg-white mix-blend-overlay transition-transform duration-3000 ease-in-out"></div>
    <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-purple-300 mix-blend-overlay transition-transform duration-3000 ease-in-out"></div>
    <div className="absolute top-1/3 right-1/4 w-80 h-80 rotate-45 bg-cyan-300 mix-blend-overlay transition-transform duration-3000 ease-in-out"></div>
  </div>
);