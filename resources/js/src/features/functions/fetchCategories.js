import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

// Thunk: Fetch Categories
export const fetchCategories = createAsyncThunk(
  "categories/fetchCategories",
  async () => {
    const response = await api.get(`/categories`);
    return response.data.categories;
  }
);