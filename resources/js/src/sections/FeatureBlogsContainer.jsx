import React, { memo } from "react";
import { motion } from "framer-motion";
import { useCategories } from "../hooks/useCategories";
import { FeatureBlogs } from "../components";
import LatestBlogsList from "../components/LatestBlogsList";

const FeatureBlogsContainer = () => {
  const [categories, handleClick] = useCategories();

  return (
    <div className="w-full font-sans px-4 sm:px-6 lg:px-8 py-10 bg-gray-50">
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {/* Left: Feature Blogs */}
        <div className="lg:col-span-9 space-y-12">
          {categories.map((category) =>
            category.blogs.length > 0 ? (
              <FeatureBlogs
                key={category.id}
                category={category}
                handleClick={handleClick}
              />
            ) : null
          )}
        </div>

        {/* Right: Sidebar */}
        <div className="lg:col-span-3">
          <div className="sticky top-0">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.4 }}
              className="p-4"
            >
              <LatestBlogsList />
            </motion.div>
          </div>
        </div>

      </div>
    </div>
  );
};

export default memo(FeatureBlogsContainer);
