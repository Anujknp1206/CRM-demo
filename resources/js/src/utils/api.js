import axios from "axios";
import { API_URL, FRONTEND_TOKEN } from "./config";

export const api = axios.create({
  baseURL: API_URL,
  // timeout: 10000,
  headers: {
    Accept: 'application/json',
    // 'Frontend-Token': `${FRONTEND_TOKEN}`,
  },
});