import React, { memo } from "react";
import { motion } from "framer-motion";
import { fetchLatestBlogs } from "../features/functions";
import { useDispatch, useSelector } from "react-redux";
import { LoaderPage } from ".";
import { Link } from "react-router-dom";

const itemVariants = {
    hidden: { opacity: 0, x: -20 },
    visible: {
      opacity: 1,
      x: 0,
      transition: { duration: 0.4 },
    },
  };

function LatestBlogsList() {

  const dispatch = useDispatch();
  const {latestBlogs, loading} = useSelector((state) => state.blogsData);

  if (!latestBlogs?.length > 0) dispatch(fetchLatestBlogs());


      if (loading) {
        return <LoaderPage isLoading={loading} />;
      }

  return (
    <motion.div
    className="h-full"
    initial="hidden"
    animate="visible"
    variants={{ visible: { transition: { staggerChildren: 0.1 } } }}
  >
    <motion.h3
      className="font-bold text-2xl mb-4 border-b pb-2"
      variants={itemVariants}
    >
      Latest Blogs
    </motion.h3>
    <ul className="space-y-4">
      {latestBlogs?.length > 0 ? latestBlogs?.map((item) => (
        <motion.li
          key={item.id}
          className="border-b pb-2 last:border-none"
          variants={itemVariants}
          whileHover={{ x: 4 }}
        >
          <Link

            to={`/blogs/${item.slug}`}
            className="text-md text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 block"
          >
            {item.title}
          </Link>
          <div className="text-md text-gray-400 mt-1">{item.post_date}</div>
        </motion.li>
      )) : (
        <p>No blogs found</p>
      )}
    </ul>
  </motion.div>
  );
}

export default memo(LatestBlogsList);
