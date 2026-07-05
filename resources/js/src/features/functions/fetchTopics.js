import { createAsyncThunk } from "@reduxjs/toolkit";
import { api } from "../../utils/api";

export const fetchTopics = createAsyncThunk("topics/fetchTopics", async () => {
  const response = await api.get('/topics');
  return response.data.topics;
});