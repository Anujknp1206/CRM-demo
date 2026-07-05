import React from "react";
import { HeroBlog, BlogListItem } from "..";


export const FeatureBlogsOne = ({ blogs, handleCick }) => (
  <HeroBlog blog={blogs[0]} />
);


export const FeatureBlogsTwo = ({ blogs, handleCick }) => (
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {blogs.map((blog) => (
      <HeroBlog key={blog.id} blog={blog} />
    ))}
  </div>
);


export const FeatureBlogsThree = ({ blogs, handleCick }) => (
  <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
    <div className="md:col-span-8 lg:pr-6">
      <HeroBlog blog={blogs[0]} />
    </div>
    <div className="md:col-span-4 space-y-6 flex flex-col justify-evenly">
      {blogs.slice(1).map((blog) => (
        <BlogListItem key={blog.id} blog={blog} />
      ))}
    </div>
  </div>
);


export const FeatureBlogsFour = ({ blogs }) => (
  <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
    {/* Main blog */}
    <div className="md:col-span-8 lg:pr-6">
      <HeroBlog blog={blogs[0]} />
    </div>

    {/* Side blogs */}
    <div className="md:col-span-4 flex flex-col gap-6">
        {blogs.slice(1, 4).map((blog) => (
            <BlogListItem key={blog.id} blog={blog} variant="side" />
        ))}
    </div>

  </div>
);



export const featuredVariantsMap = {
  1: FeatureBlogsOne,
  2: FeatureBlogsTwo,
  3: FeatureBlogsThree,
  4: FeatureBlogsFour, // Add this line
  default: FeatureBlogsFour,
};

