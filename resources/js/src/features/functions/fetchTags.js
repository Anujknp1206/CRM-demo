import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchTags = createAsyncThunk("tags/fetchTags", async () => {
  const response = await api.get(`/tags`);
  return response.data.tags;
});