import { createSlice } from "@reduxjs/toolkit";
import { fetchBlogs, fetchBlogDetail, fetchLatestBlogs } from "../functions";

const initialState = {
  blogs: [],
  currentPage: 1,
  lastPage: 1,

  blog: null,
  relatedBlogs: [],

  latestBlogs: [],

  imgUrl: "",
  loading: false,
  error: null,

  filters: {
    topics: null,
    tags: [],
    categories: [],
    searchQuery: "",
  },
};

// Slice
const blogSlice = createSlice({
  name: "blogsData",
  initialState,
  reducers: {
    setFilters: (state, action) => {
      state.filters = {
        ...state.filters,
        ...action.payload,
      };
      state.blogs = [];
      state.currentPage = 1;
    },
    resetBlogs: (state) => {
      state.blogs = [];
      state.currentPage = 1;
      state.lastPage = 1;
    },
  },
  extraReducers: (builder) => {
    builder
      // Blog List
      .addCase(fetchBlogs.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchBlogs.fulfilled, (state, action) => {
        state.loading = false;

        const newBlogs = action.payload.blogs.filter(
          (newBlog) => !state.blogs.some((existingBlog) => existingBlog.id === newBlog.id)
        );

        state.blogs.push(...newBlogs);
        state.imgUrl = action.payload.imgUrl;
        state.lastPage = action.payload.last_page;
        state.currentPage = action.payload.current_page;
      })
      .addCase(fetchBlogs.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      })

      // Blog Detail
      .addCase(fetchBlogDetail.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchBlogDetail.fulfilled, (state, action) => {
        state.loading = false;
        state.blog = action.payload.blog;
        state.relatedBlogs = action.payload.related_blogs;
        state.imgUrl = action.payload.imgUrl;
      })
      .addCase(fetchBlogDetail.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      })

      // Latest Blogs
      .addCase(fetchLatestBlogs.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchLatestBlogs.fulfilled, (state, action) => {
        state.loading = false;
        state.latestBlogs = action.payload.latestBlogs;
      })
      .addCase(fetchLatestBlogs.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      });
  },
});

export const { setFilters, resetBlogs } = blogSlice.actions;
export default blogSlice.reducer;