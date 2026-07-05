import { createSlice } from '@reduxjs/toolkit';

const loadingSlice = createSlice({
  name: 'globalLoading',
  initialState: {
    isLoading: false,
    loadingRequests: 0,
  },
  reducers: {
    startLoading: (state) => {
      state.loadingRequests += 1;
      state.isLoading = true;
    },
    stopLoading: (state) => {
      state.loadingRequests = 0;
      state.isLoading = false;
    },
    resetLoading: (state) => {
      state.loadingRequests = 0;
      state.isLoading = false;
    }
  },
});

export const { startLoading, stopLoading, resetLoading } = loadingSlice.actions;
export default loadingSlice.reducer;