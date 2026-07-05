import React, { memo, useEffect, useState } from "react";
import { RiSearch2Line } from "@remixicon/react";
import { useDispatch, useSelector } from "react-redux";
import { useLocation, useNavigate } from "react-router-dom";
import { fetchCategories } from "../features/functions";
import { setFilters, resetBlogs } from "../features/slices/blogSlice";
import { fetchBlogs } from "../features/functions/fetchBlogs";

function SearchBox() {
  const dispatch = useDispatch();
  const location = useLocation();
  const navigate = useNavigate();

  // Get current search query from Redux store
  const { searchQuery } = useSelector((state) => state.blogsData.filters);
  const [searchTerm, setSearchTerm] = useState(searchQuery || "");

  // Always sync with Redux store's search query
  useEffect(() => {
    setSearchTerm(searchQuery || "");
  }, [searchQuery]);

  // Handle initial URL state
  useEffect(() => {
    // Handle search query from URL state on component mount
    if (location.state?.searchQuery) {
      const query = location.state.searchQuery;
      // Clean up location state after using it
      navigate(location.pathname, { replace: true, state: {} });
      // Update Redux filters
      dispatch(resetBlogs());
      dispatch(setFilters({ searchQuery: query }));
      dispatch(fetchBlogs({ page: 1, filters: { searchQuery: query } }));
    }
  }, [dispatch, location.state, navigate, location.pathname]);

  useEffect(() => {
    dispatch(fetchCategories());
  }, [dispatch]);

  const { categories } = useSelector((state) => state.categories);

  const basePlaceholder = "Search";
  const placeholders =
    Array.isArray(categories) && categories.length > 0
      ? [...new Set(categories.map((cat) => `${basePlaceholder} ${cat?.name}`))]
      : [basePlaceholder];

  const [currentValue, setCurrentValue] = useState("");
  const [placeholderIndex, setPlaceholderIndex] = useState(0);
  const [charIndex, setCharIndex] = useState(0);

  useEffect(() => {
    let typingInterval;
    const currentPlaceholder =
      placeholders[placeholderIndex] || basePlaceholder;

    if (charIndex >= currentPlaceholder.length) {
      typingInterval = setTimeout(() => {
        setCurrentValue("");
        setPlaceholderIndex((prev) => (prev + 1) % placeholders.length);
        setCharIndex(0);
      }, 1500);
    } else {
      typingInterval = setTimeout(() => {
        setCurrentValue(currentPlaceholder.substring(0, charIndex + 1));
        setCharIndex((prev) => prev + 1);
      }, 100);
    }

    return () => clearTimeout(typingInterval);
  }, [currentValue, charIndex, placeholderIndex, placeholders]);

  const handleSearch = () => {
    const trimmed = searchTerm.trim();

    // Reset blogs before new search
    dispatch(resetBlogs());

    if (trimmed) {
      // Update search term filter in redux and fetch blogs
      dispatch(setFilters({ searchQuery: trimmed }));
      dispatch(fetchBlogs({ page: 1, filters: { searchQuery: trimmed } }));
    } else {
      // If search is empty, clear the search query filter
      dispatch(setFilters({ searchQuery: "" }));
      dispatch(fetchBlogs({ page: 1, filters: { searchQuery: "" } }));
    }
  };

  // Handle input change (local only)
  const handleInputChange = (e) => {
    if (e.target.value === "") {
      setSearchTerm("");
      dispatch(resetBlogs());
      dispatch(setFilters({ searchQuery: "" }));
      dispatch(fetchBlogs({ page: 1, filters: { searchQuery: "" } }));
    }
    setSearchTerm(e.target.value);
  };

  const handleKeyPress = (e) => {
    if (e.key === "Enter") {
      handleSearch();
    }
  };

  return (
    <div className="flex flex-col gap-2 px-4 py-4 shadow-2xl w-full box-border">
      <h3 className="font-bold text-lg">Search</h3>
      <div className="flex w-full min-w-0 relative">
        <input
          type="text"
          placeholder={currentValue}
          value={searchTerm}
          onChange={handleInputChange}
          onKeyDown={handleKeyPress}
          className="flex-1 min-w-0 px-3 py-2 border border-gray-200 rounded-l-md outline-none"
        />
        <button
          onClick={handleSearch}
          className="bg-[#1e38a3] text-white hover:bg-white hover:text-blue-800 transition-all duration-150 border-blue-800 border hover:font-bold px-4 py-2 text-sm font-bold rounded-r-md flex items-center justify-center cursor-pointer"
        >
          <RiSearch2Line size={18} />
        </button>
      </div>
    </div>
  );
}

export default memo(SearchBox);
