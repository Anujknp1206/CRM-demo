import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchPages = createAsyncThunk("pages/fetchPages", async (slug) => {
  const response = await api.get(`/page/${slug}`);
  return response.data;
});