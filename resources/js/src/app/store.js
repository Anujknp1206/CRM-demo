import { configureStore } from "@reduxjs/toolkit";
import reducer from "../features/reducers";

export const store = configureStore({
  reducer: reducer,
});
