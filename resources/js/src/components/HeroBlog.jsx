import React, { memo } from 'react';
import { motion } from 'framer-motion';
import { BASE_IMG_URL } from '../utils/config';
import { Link } from 'react-router-dom';

const HeroBlog = ({ blog }) => {
  const bannerImage = blog?.banner
    ? `${BASE_IMG_URL}/blog/${blog.banner}`
    : `${BASE_IMG_URL}/no-image.png`;

  return (
    <Link to={`/blogs/${blog.slug}`}>
      <motion.div
        className="h-full overflow-hidden rounded-xl cursor-pointer"
        transition={{ duration: 1 }}
      >
        {/* Image container without shadow */}
        <motion.div
          className="h-80 overflow-hidden rounded-xl mb-6"
          whileHover={{ scale: 1.01 }}
        >
          <img
            src={bannerImage}
            alt={blog?.title || 'Blog Banner'}
            className="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
          />
        </motion.div>

        {/* Blog content */}
        <div className="space-y-3">
          <motion.h2
            className="text-2xl font-bold text-gray-800 leading-tight capitalize"
            whileHover={{ color: "#3B82F6" }}
          >
            {blog?.title}
          </motion.h2>

          <p className="text-justify text-xl">
            {blog?.short_description}
          </p>

          <div className="flex items-center space-x-2">
            <Link
              to={`/author/${blog?.user?.display_name}`}
              onClick={(e) => e.stopPropagation()}
              className="text-gray-600 font-medium hover:text-blue-600 text-xl"
            >
              &nbsp;@{blog?.user?.display_name}
            </Link>
            <span className="text-gray-400">•</span>
            <span className="text-gray-500 text-xl">{blog?.post_date}</span>
          </div>
        </div>
      </motion.div>
    </Link>
  );
};

export default memo(HeroBlog);
