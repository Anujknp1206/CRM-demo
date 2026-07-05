import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchAuthorDetail = createAsyncThunk(
  "Author/fetchAuthorDetail",
  async (slug) => {
    const response = await api.get(`/author/${slug}`);
    return response.data;
  }
);