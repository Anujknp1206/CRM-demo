import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchSettings = createAsyncThunk("settings/fetchSettings", async () => {
  const response = await api.get(`/settings`);
  return response.data;
});