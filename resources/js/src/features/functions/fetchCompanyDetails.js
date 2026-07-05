import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchCompanies = createAsyncThunk(
  "companies/fetchCompanies",
  async (page = 1) => {
    const response = await api.get(`/companies?page=${page}`);
    return { ...response.data, page };
  }
);

export const fetchCompany = createAsyncThunk(
  "company/fetchCompany",
  async (slug) => {
    const response = await api.get(`/company/${slug}`);
    return response.data;
  }
);

export const fetchAllCompanies = createAsyncThunk(
  "companies/fetchAllCompanies",
  async () => {
    const response = await api.get(`/all-companies`);
    return response.data;
  }
)
