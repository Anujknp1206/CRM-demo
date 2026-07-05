import { createSelector } from "@reduxjs/toolkit";

export const selectIsLoading = createSelector(
  (state) => state.settingsData.loading,
  (state) => state.AuthorData.loading,
  (state) => state.categories.loading,
  (state) => state.companies.loading,
  (state) => state.menuItems.loading,
  (state) => state.pagesData.loading,
  (state) => state.topics.loading,
  (state) => state.tags.loading,
  (
    settingsLoading,
    authorLoading,
    categoriesLoading,
    companiesLoading,
    menuItemsLoading,
    pagesLoading,
    topicsLoading,
    tagsLoading
  ) =>
    settingsLoading ||
    authorLoading ||
    categoriesLoading ||
    companiesLoading ||
    menuItemsLoading ||
    pagesLoading ||
    topicsLoading ||
    tagsLoading
);