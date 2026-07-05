import React, { useState, useEffect, memo, useCallback } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { RiMenu3Line, RiSearch2Line, RiCloseLine } from "@remixicon/react";
import { Logo, NavLinks, NavMobileMenu } from ".";
import { useSelector, useDispatch } from "react-redux";
import { fetchMenuItems, fetchCategories, fetchBlogs } from "../features/functions";
import { setFilters, resetBlogs } from "../features/slices/blogSlice";

const Navbar = () => {
  const dispatch = useDispatch();
  const location = useLocation();
  const navigate = useNavigate();

  // State management
  const [showMenu, setShowMenu] = useState(false);
  const [showSearch, setShowSearch] = useState(false);
  const [showSearchAfterScroll, setShowSearchAfterScroll] = useState(false);

  // Path check
  const isParentBlogPage = location.pathname.startsWith("/blogs");

  // Redux state
  const { searchQuery } = useSelector((state) => state.blogsData.filters);
  const { categories } = useSelector((state) => state.categories);
  const { menuItems, loading, error } = useSelector((state) => state.menuItems);

  // Local state for search
  const [navbarSearchTerm, setNavbarSearchTerm] = useState(searchQuery || "");

  // Search placeholder animation vars
  const basePlaceholder = "Search";
  const placeholders = Array.isArray(categories) && categories.length > 0
    ? [...new Set(categories.map((cat) => `${basePlaceholder} ${cat?.name}`))]
    : [basePlaceholder];
  const [currentValue, setCurrentValue] = useState("");
  const [placeholderIndex, setPlaceholderIndex] = useState(0);
  const [charIndex, setCharIndex] = useState(0);

  // Show search bar when not on blog page or after scrolling
  const showSearchBar = !isParentBlogPage || showSearchAfterScroll;

  // Sync with Redux store's search query
  useEffect(() => {
    setNavbarSearchTerm(searchQuery || "");
  }, [searchQuery]);

  // Fetch nav items and categories on mount
  useEffect(() => {
    dispatch(fetchMenuItems());
    dispatch(fetchCategories());
  }, [dispatch]);

  // Placeholder typing effect
  useEffect(() => {
    let typingInterval;
    const currentPlaceholder = placeholders[placeholderIndex] || basePlaceholder;

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

  // Scroll listener to show search
  useEffect(() => {
    const handleScrollUpdate = (e) => {
      setShowSearchAfterScroll(e.detail === true);
    };

    window.addEventListener("parentSearchPosition", handleScrollUpdate);
    return () => window.removeEventListener("parentSearchPosition", handleScrollUpdate);
  }, []);

  // Search handler
  const handleNavbarSearch = useCallback(() => {
    const trimmed = navbarSearchTerm.trim();

    // Reset blogs before performing search
    if (location.pathname === "/blogs") {
      dispatch(resetBlogs());
    }

    if (trimmed) {
      if (location.pathname !== "/blogs") {
        // Navigate to blogs page with search query
        navigate("/blogs", {
          replace: false,
          state: { searchQuery: trimmed },
        });
      } else {
        // Update search query in Redux and fetch filtered blogs
        dispatch(setFilters({ searchQuery: trimmed }));
        dispatch(fetchBlogs({ page: 1, filters: { searchQuery: trimmed } }));
      }
    } else {
      // If search is empty, clear the search query
      if (location.pathname === "/blogs") {
        dispatch(setFilters({ searchQuery: "" }));
        dispatch(fetchBlogs({ page: 1, filters: { searchQuery: "" } }));
      } else {
        navigate("/blogs");
      }
    }

    // Close mobile search if open
    setShowSearch(false);
  }, [dispatch, location.pathname, navbarSearchTerm, navigate]);

  // Handle input change
  const handleNavbarInputChange = useCallback((e) => {
    const value = e.target.value;
    setNavbarSearchTerm(value);

    if (value === "") {
      dispatch(resetBlogs());
      dispatch(setFilters({ searchQuery: "" }));
      dispatch(fetchBlogs({ page: 1, filters: { searchQuery: "" } }));
    }
  }, [dispatch]);

  // Clear search handler
  const handleClearSearch = useCallback(() => {
    setNavbarSearchTerm("");
    dispatch(resetBlogs());
    dispatch(setFilters({ searchQuery: "" }));
    dispatch(fetchBlogs({ page: 1, filters: { searchQuery: "" } }));
  }, [dispatch]);

  // Early return for errors
  if (error) return <div className="text-red-500">Error loading navigation: {error.message}</div>;

  return (
    <>
      <nav className="sticky top-0 z-50 flex justify-between items-center p-4 h-16 bg-[#f7f7f7] shadow-lg">
        <div className="flex-shrink-0">
          <Logo />
        </div>

        {/* Search bar - desktop */}
        {showSearchBar && (
          <div className="hidden md:flex items-center flex-1 max-w-md mx-4">
            <div className="flex w-full min-w-0 relative">
              <input
                type="text"
                value={navbarSearchTerm}
                onChange={handleNavbarInputChange}
                onKeyDown={(e) => e.key === "Enter" && handleNavbarSearch()}
                placeholder={currentValue}
                className="flex-1 min-w-0 px-3 py-1 border border-gray-200 rounded-l-md outline-none"
                aria-label="Search"
              />
              {navbarSearchTerm && (
                <button
                  onClick={handleClearSearch}
                  className="absolute right-10 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  aria-label="Clear search"
                >
                  <RiCloseLine size={16} />
                </button>
              )}
              <button
                onClick={handleNavbarSearch}
                className="bg-[#1e38a3] text-white hover:bg-white hover:text-blue-800 transition-all duration-150 border-blue-800 border hover:font-bold px-3 py-1 text-sm font-bold rounded-r-md flex items-center justify-center cursor-pointer"
                aria-label="Search"
              >
                <RiSearch2Line size={16} />
              </button>
            </div>
          </div>
        )}

        {/* Desktop Nav */}
        <div className="hidden md:flex">
          <NavLinks navLinks={menuItems} classNameLi="text-black" />
        </div>

        {/* Mobile Controls */}
        <div className="flex md:hidden items-center gap-4">
          {showSearchBar && (
            <button
              onClick={() => setShowSearch(true)}
              className="text-blue-600"
              aria-label="Open search"
            >
              <RiSearch2Line size={20} />
            </button>
          )}
          <button
            onClick={() => setShowMenu(true)}
            className="text-blue-600"
            aria-label="Open menu"
          >
            <RiMenu3Line size={25} />
          </button>
        </div>
      </nav>

      {/* Mobile Nav Menu */}
      <NavMobileMenu
        navLinks={menuItems}
        isOpen={showMenu}
        onClose={() => setShowMenu(false)}
      />

      {/* Mobile Search Overlay */}
      {showSearch && showSearchBar && (
        <div className="fixed inset-0 z-50 bg-white p-4">
          <div className="flex items-center gap-4 mb-4">
            <button
              onClick={() => setShowSearch(false)}
              className="text-blue-600"
            >
              Back
            </button>
            <h3 className="font-bold text-lg">Search</h3>
          </div>
          <div className="flex w-full min-w-0 relative">
            <input
              type="text"
              value={navbarSearchTerm}
              onChange={handleNavbarInputChange}
              onKeyDown={(e) => e.key === "Enter" && handleNavbarSearch()}
              placeholder={currentValue}
              className="flex-1 min-w-0 px-3 py-2 border border-gray-200 rounded-l-md outline-none"
              aria-label="Search"
            />
            {navbarSearchTerm && (
              <button
                onClick={handleClearSearch}
                className="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                aria-label="Clear search"
              >
                <RiCloseLine size={18} />
              </button>
            )}
            <button
              onClick={handleNavbarSearch}
              className="bg-[#1e38a3] text-white hover:bg-white hover:text-blue-800 transition-all duration-150 border-blue-800 border hover:font-bold px-4 py-2 text-sm font-bold rounded-r-md flex items-center justify-center cursor-pointer"
              aria-label="Search"
            >
              <RiSearch2Line size={18} />
            </button>
          </div>
        </div>
      )}
    </>
  );
};

export default memo(Navbar);