import React, { memo, useEffect } from "react";
import { FilterDropdown } from ".";
import { useDispatch, useSelector } from "react-redux";
import { fetchCategories } from "../features/functions";
import { fetchTags } from "../features/functions";
import { fetchBlogs } from "../features/functions/fetchBlogs";
import { setFilters } from "../features/slices/blogSlice";
import { useNavigate, useSearchParams, useLocation } from "react-router-dom";

function Filters() {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const location = useLocation();
  const [searchParams, setSearchParams] = useSearchParams();

  const { filters } = useSelector((state) => state.blogsData);

  const {
    categories,
    loading: catLoading,
    error: catError,
  } = useSelector((state) => state.categories);

  const {
    tags,
    loading: tagLoading,
    error: tagError,
  } = useSelector((state) => state.tags);

  const loading = catLoading || tagLoading;
  const error = catError || tagError;

  // Fetch options
  useEffect(() => {
    dispatch(fetchCategories());
    dispatch(fetchTags());
  }, [dispatch]);

  // Sync Redux filters from URL when on /blogs
  useEffect(() => {
    if (location.pathname === "/blogs") {
      const categories = searchParams.get("categories")?.split(",") || [];
      const tags = searchParams.get("tags")?.split(",") || [];

      const urlFilters = { categories, tags };
      dispatch(setFilters(urlFilters));
    }
  }, [location.pathname, searchParams, dispatch]);

  // Fetch blogs when Redux filters change
  useEffect(() => {
    if (location.pathname === "/blogs") {
      dispatch(fetchBlogs({ page: 1, filters }));
    }
  }, [filters, location.pathname, dispatch]);

  const categoryOptions = Array.isArray(categories)
    ? categories.map((cat) => ({ id: cat.id.toString(), label: cat.name }))
    : [];

  const tagOptions = Array.isArray(tags)
    ? tags.map((tag) => ({ id: tag, label: tag }))
    : [];

  const handleFilterChange = (filterType, values) => {
    let updatedFilters = { ...filters };

    if (filterType === "categories") {
      updatedFilters.categories = values;
    } else if (filterType === "tags") {
      updatedFilters.tags = values;
    }

    // Update Redux
    dispatch(setFilters(updatedFilters));

    // Update URL
    const params = new URLSearchParams();
    if (updatedFilters.categories?.length) {
      params.set("categories", updatedFilters.categories.join(","));
    }
    if (updatedFilters.tags?.length) {
      params.set("tags", updatedFilters.tags.join(","));
    }

    setSearchParams(params);
    navigate(`/blogs/?${params.toString()}`);
  };

  return (
    <div className="flex flex-col gap-2 p-4">
      <h3 className="font-bold text-lg">Filters</h3>

      {loading && (
        <div className="text-gray-500 text-sm">Loading filters...</div>
      )}
      {error && (
        <div className="text-red-500 text-sm">Error loading filters</div>
      )}

      {!loading && !error && (
        <div className="flex flex-col gap-4 mt-2">
          <FilterDropdown
            name="Categories"
            options={categoryOptions}
            selected={filters.categories || []}
            onChange={handleFilterChange}
          />
          <FilterDropdown
            name="Tags"
            options={tagOptions}
            selected={filters.tags || []}
            onChange={handleFilterChange}
          />
        </div>
      )}
    </div>
  );
}

export default memo(Filters);
