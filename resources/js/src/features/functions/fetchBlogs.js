import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

// Thunk: Fetch Blogs with Filters and Search
export const fetchBlogs = createAsyncThunk(
  "blogs/fetchBlogs",
  async ({ page = 1, filters = {} }) => {
    const { topics, tags, categories, searchQuery } = filters;

    const query = new URLSearchParams();
    query.append("page", page);
    if (topics) query.append("topics", topics);
    if (tags?.length) query.append("tags", tags.join(","));
    if (categories?.length) query.append("categories", categories.join(","));
    if (searchQuery) query.append("q", searchQuery);

    const response = await api.get(`/blogs?${query.toString()}`);
    return response.data;
  }
);

// Thunk: Fetch Blog Detail by Slug
export const fetchBlogDetail = createAsyncThunk(
  "blogs/fetchBlogDetail",
  async (slug) => {
    const response = await api.get(`/blog/${slug}`);
    return response.data;
  }
);

export const fetchLatestBlogs = createAsyncThunk(
  "blogs/fetchLatestBlogs",
  async () => {
    const response = await api.get(`/latest-blogs`);
    return response.data;
  }
);
