import React, { memo } from "react";
import { BASE_IMG_URL } from "../utils/config";
import { Link } from "react-router-dom";

const BlogListItem = ({ blog, variant = "main" }) => {
  const bannerImage = blog?.banner
    ? `${BASE_IMG_URL}/blog/${blog.banner}`
    : `${BASE_IMG_URL}/no-image.png`;

  if (variant === "side") {
    return (
  <Link
    to={`/blogs/${blog.slug}`}
    className="flex items-start gap-4 pb-4 mb-4 border-b border-dotted border-gray-300 last:border-b-0 last:pb-0 last:mb-0"
  >
    {/* Text content */}
    <div className="flex-1 space-y-1">
      <h3 className="text-gray-800 text-[17px] leading-snug hover:text-blue-600 transition capitalize font-medium">
        {blog.title}
      </h3>
      <div className="text-sm text-gray-500 flex gap-1 flex-wrap">
        <Link
          to={`/author/${blog.user.display_name}`}
          onClick={(e) => e.stopPropagation()}
          className="hover:text-blue-600"
        >
          @{blog.user.display_name}
        </Link>
        <span>•</span>
        <span>{blog.post_date}</span>
      </div>
    </div>

    {/* Thumbnail */}
    <div className="w-24 h-20 flex-shrink-0 overflow-hidden rounded-md">
      <img
        src={bannerImage}
        alt={blog.title}
        className="w-full h-full object-cover"
      />
    </div>
  </Link>
);

  }

  // Default large blog layout
  return (
  <Link
    to={`/blogs/${blog.slug}`}
    className="hover:bg-gray-100 transition cursor-pointer border-b border-dotted border-gray-300 overflow-hidden mb-4 pb-4 last:mb-0 last:pb-0"
  >
    <img
      src={bannerImage}
      alt={blog.title}
      className="w-full h-48 object-cover"
    />
    <div className="p-4">
      <h3 className="text-gray-800 text-xl leading-snug hover:text-blue-600 transition capitalize">
        {blog.title}
      </h3>
      <div className="text-sm text-gray-500 mt-2 flex gap-1 flex-wrap">
        <Link
          to={`/author/${blog.user.display_name}`}
          onClick={(e) => e.stopPropagation()}
          className="hover:text-blue-600"
        >
          @{blog.user.display_name}
        </Link>
        <span>•</span>
        <span>{blog.post_date}</span>
      </div>
    </div>
  </Link>
);

};

export default memo(BlogListItem);

