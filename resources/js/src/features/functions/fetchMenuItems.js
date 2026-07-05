import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchMenuItems = createAsyncThunk("menuItems/fetchMenuItems", async () => {
  const response = await api.get(`/menu-items`);
  return response.data.menuItems;
});