import React, { memo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { RiSearch2Line } from "@remixicon/react";

const loaderVariants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      duration: 0.1,
    },
  },
  exit: {
    opacity: 0,
    transition: {
      duration: 0.5,
      when: "afterChildren",
    },
  },
};

const iconVariants = {
  hidden: { scale: 0.8, opacity: 0 },
  visible: {
    scale: 1,
    opacity: 1,
    transition: {
      type: "spring",
      damping: 10,
      stiffness: 100,
    },
  },
  animate: {
    rotate: [0, 5, -5, 0],
    y: [0, -5, 0],
    transition: {
      repeat: Infinity,
      duration: 1.8,
      ease: "easeInOut",
    },
  },
};

const dotVariants = {
  animate: (i) => ({
    y: [0, -8, 0],
    opacity: [0.3, 1, 0.3],
    transition: {
      delay: i * 0.15,
      repeat: Infinity,
      duration: 1.2,
      ease: "easeInOut",
    },
  }),
};

const LoaderPage = ({ isLoading }) => {
  return (
    <AnimatePresence>
      {isLoading && (
        <motion.div
          className="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center gap-6"
          variants={loaderVariants}
          initial={false}
          animate="visible"
          exit="exit"
        >
          {/* Animated magnifying glass icon */}
          <motion.div
            className="relative text-blue-600"
            variants={iconVariants}
            initial="hidden"
            animate={["visible", "animate"]}
            style={{ fontSize: "4rem" }}
          >
            <RiSearch2Line />
            {/* Pulsing circle effect */}
            <motion.span
              className="absolute inset-0 border-2 border-blue-600 rounded-full -m-2"
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{
                scale: [1, 1.2, 1],
                opacity: [0.6, 0, 0.6],
              }}
              transition={{
                repeat: Infinity,
                duration: 1.8,
                ease: "easeInOut",
              }}
            />
          </motion.div>

          {/* Animated dots */}
          <div className="flex gap-2">
            {[0, 1, 2].map((i) => (
              <motion.span
                key={i}
                className="w-2 h-2 bg-blue-600 rounded-full"
                variants={dotVariants}
                animate="animate"
                custom={i}
              />
            ))}
          </div>

          {/* Text with fade animation */}
          <motion.p
            className="text-gray-700 font-medium text-lg"
            initial={{ opacity: 0 }}
            animate={{
              opacity: [0, 1, 0.8],
              transition: { duration: 1.5, repeat: Infinity },
            }}
          >
            Finding anything...
          </motion.p>
        </motion.div>
      )}
    </AnimatePresence>
  );
};

export default memo(LoaderPage);
