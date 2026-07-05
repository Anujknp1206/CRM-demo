import React, { memo, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchBlogs } from "../features/functions/fetchBlogs";
import { Blog, PulseLoader } from ".";
import { BASE_IMG_URL } from "../utils/config";

function Blogs() {
  const dispatch = useDispatch();

  const { blogs, currentPage, lastPage, loading, error } = useSelector(
    (state) => state.blogsData
  );

  const { topics, tags, categories, searchQuery } = useSelector(
    (state) => state.blogsData.filters
  );

  useEffect(() => {
    const filtersReady = [topics, tags, categories, searchQuery].every(
      (f) => f !== undefined
    );
    const isFilterActive =
      Boolean(searchQuery?.trim()) ||
      (topics && topics.length > 0) ||
      (tags && tags.length > 0) ||
      (categories && categories.length > 0);

    // Only fetch blogs on initial mount or filter change when blogs are empty
    if (filtersReady && blogs.length === 0) {
      dispatch(
        fetchBlogs({
          page: 1,
          filters: { topics, tags, categories, searchQuery },
        })
      );
    }
  }, [dispatch, topics, tags, categories, searchQuery, blogs.length]);

  const loadMore = () => {
    if (currentPage < lastPage && !loading) {
      dispatch(
        fetchBlogs({
          page: currentPage + 1,
          filters: { topics, tags, categories, searchQuery },
        })
      );
    }
  };

  const renderSearchInfo = () => {
    if (searchQuery && blogs.length > 0) {
      return (
        <div className="mb-6 text-gray-700">
          <p>
            Showing search results for:{" "}
            <span className="font-bold">"{searchQuery}"</span>
          </p>
        </div>
      );
    } else if (searchQuery && blogs.length === 0 && !loading) {
      return (
        <div className="mb-6 text-gray-700">
          <p>
            No results found for:{" "}
            <span className="font-bold">"{searchQuery}"</span>
          </p>
        </div>
      );
    }
    return null;
  };

  if (loading && blogs.length === 0) {
    return <PulseLoader />;
  }

  if (error) {
    return (
      <div className="text-red-600 text-center py-10">
        Failed to load blogs: {error}
      </div>
    );
  }

  if (blogs.length === 0 && !loading) {
    return (
      <div className="py-10">
        {renderSearchInfo() || (
          <div className="text-center text-gray-600">
            No blogs available at the moment.
          </div>
        )}
      </div>
    );
  }

  return (
    <div>
      {renderSearchInfo()}

      <div className="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-5">
        {blogs.map((blog) => (
          <Blog
            key={blog.id}
            title={blog.title}
            author={blog.user.display_name}
            description={blog.short_description}
            image={
              blog.banner === "" ? "" : `${BASE_IMG_URL}blog/` + blog.banner
            }
            date={blog.post_date}
            link={blog.slug}
          />
        ))}
      </div>

      {currentPage < lastPage && (
        <div className="flex justify-center mt-6">
          <button
            className="bg-blue-700 px-4 py-2 rounded text-white cursor-pointer border border-blue-700 hover:bg-white hover:text-blue-700 transition-all duration-150"
            onClick={loadMore}
            disabled={loading}
          >
            {loading ? "Loading..." : "Show More"}
          </button>
        </div>
      )}
    </div>
  );
}

export default memo(Blogs);
