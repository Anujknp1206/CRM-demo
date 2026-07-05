import { createSlice } from "@reduxjs/toolkit";
import { fetchPages } from "../functions";

const initialState = {
  pagesData: null,
  loading: false,
  error: null,
}

const pageSlice = createSlice({
  name: "pagesData",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchPages.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchPages.fulfilled, (state, action) => {
        state.loading = false;
        state.pagesData = action.payload.page;
      })
      .addCase(fetchPages.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      });
  },
});

export default pageSlice.reducer;
