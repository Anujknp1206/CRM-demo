import { createSlice } from "@reduxjs/toolkit";
import { fetchSettings } from "../functions";

const initialState = {
  settingsData: [],
  loading: false,
  error: null,
};

const settingSlice = createSlice({
  name: "settingsData",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchSettings.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchSettings.fulfilled, (state, action) => {
        state.loading = false;
        state.settingsData = action.payload;
      })
      .addCase(fetchSettings.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      });
  },
});

export default settingSlice.reducer;