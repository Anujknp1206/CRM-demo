import blogsDataReducer from "../slices/blogSlice";
import topicsReducer from "../slices/topicSlice";
import menuItemsReducer from "../slices/menuItemSlice";
import categoriesReducer from "../slices/categorieSlice";
import tagsReducer from "../slices/tagSlice";
import settingsReducer from "../slices/settingSlice";
import pageReducer from "../slices/pageSlice";
import authorReducer from "../slices/authorSlice";
import companyReducer from "../slices/companySlice";
import loadingReducer from "../slices/loadingSlice";

const reducer = {
  blogsData: blogsDataReducer,
  topics: topicsReducer,
  menuItems: menuItemsReducer,
  categories: categoriesReducer,
  tags: tagsReducer,
  settingsData: settingsReducer,
  pagesData: pageReducer,
  AuthorData: authorReducer,
  companies: companyReducer,
  loading: loadingReducer,
}

export default reducer;