import React from "react";
import { motion } from "framer-motion";

function Banner({ heading = "", para = "", children = null }) {
  return (
    <div className="py-20 px-6 sm:px-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-gray-50 text-gray-800 relative overflow-hidden">

      {/* Glass-morphism background effect */}
      <div className="absolute inset-0 backdrop-blur-sm bg-white/30"></div>

      {/* Animated background elements */}
      <div className="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-cyan-200 opacity-20 animate-pulse"></div>
      <div className="absolute -bottom-10 -left-10 w-80 h-80 rounded-full bg-blue-200 opacity-20 animate-pulse delay-1000"></div>

      {/* Subtle texture overlay */}
      <div className="absolute inset-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:16px_16px] opacity-10"></div>

      <motion.div
        className="max-w-4xl mx-auto text-center relative z-10"
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true }}
        transition={{ duration: 0.6 }}
      >
        <h1 className="text-4xl sm:text-4xl font-bold mb-6 text-gray-800">{heading}</h1>
        <p className="text-lg mb-8 text-gray-600">{para}</p>
        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          {React.Children.map(children, child => (
            React.cloneElement(child, {
              // className: `${child.props.className || ''} backdrop-blur-md bg-white/70 hover:bg-white border border-gray-200 shadow-sm hover:shadow-md transition-all text-gray-700 hover:text-gray-900`
            })
          ))}
        </div>
      </motion.div>
    </div>
  );
}

export default Banner;