import { createSlice } from "@reduxjs/toolkit";
import { fetchCompanies, fetchCompany, fetchAllCompanies } from "../functions";

const initialState = {
  allCompanies: [],
  companies: [],
  company: null,
  loading: false,
  error: null,
  total: 0,
  currentPage: 1,
  totalPages: 1,
};

const companySlice = createSlice({
  name: "companies",
  initialState,

  reducers: {
    resetCompanies(state) {
      state.companies = [];
      state.currentPage = 1;
    },
    resetCompany(state) {
      state.company = null;
    },
  },

  extraReducers: (builder) => {
    builder
      // Fetch Companies List
      .addCase(fetchCompanies.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchCompanies.fulfilled, (state, action) => {
        state.loading = false;
        const { data, total, current_page, last_page, page } = action.payload;

        if (page > 1) {
          state.companies = [...state.companies, ...data];
        } else {
          state.companies = data;
        }

        state.total = total;
        state.currentPage = current_page;
        state.totalPages = last_page;
      })
      .addCase(fetchCompanies.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      })

      // Fetch Single Company Detail
      .addCase(fetchCompany.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchCompany.fulfilled, (state, action) => {
        state.loading = false;
        state.company = action.payload;
      })
      .addCase(fetchCompany.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      })

      // Fetch All Companies
      .addCase(fetchAllCompanies.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchAllCompanies.fulfilled, (state, action) => {
        state.loading = false;
        state.allCompanies = action.payload;
      })
      .addCase(fetchAllCompanies.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      });
  },
});

export const { resetCompanies, resetCompany } = companySlice.actions;
export default companySlice.reducer;