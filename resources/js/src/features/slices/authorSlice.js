import { createSlice } from "@reduxjs/toolkit";
import { fetchAuthorDetail } from "../functions";

const initialState = {
  user: {},
  imgUrl: "",
  adminImgUrl: "",
  latestBlogs: [],
  approvedBlogCount: 0,
  loading: false,
  error: null,
};

const AuthorSlice = createSlice({
  name: "AuthorData",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchAuthorDetail.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchAuthorDetail.fulfilled, (state, action) => {
        state.loading = false;
        state.user = action.payload.user;
        state.imgUrl = action.payload.imgUrl;
        state.adminImgUrl = action.payload.adminImgUrl;
        state.approvedBlogCount = action.payload.approvedBlogCount;
        state.latestBlogs = action.payload.latestBlogs;
      })
      .addCase(fetchAuthorDetail.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      });
  },
});

export default AuthorSlice.reducer;
export const { setSelectedBlogImage } = AuthorSlice.actions;
