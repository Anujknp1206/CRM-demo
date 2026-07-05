import React, { memo, useCallback } from "react";
import { Link } from "react-router-dom";

/**
 * Dropdown menu component for desktop navigation
 * @param {Array} childrenLinks - Child navigation links
 * @param {string} className - Additional CSS classes
 * @param {string} parent - Parent slug for URL construction
 */
const MenuDropdown = ({ childrenLinks = [], className = "", parent = "" }) => {
  // Render link based on link_type
  const renderChildLink = useCallback((link) => {
    switch (link.link_type) {
      case "page":
        return (
          <Link
            to={link.slug === "home" ? "/" : `/${parent}/${link.slug}`}
            className="block text-sm w-full whitespace-nowrap transition-colors hover:text-blue-600"
          >
            {link.name}
          </Link>
        );
      case "route":
        return (
          <Link
            to={link.link_value === "index" ? "/" : `/${link.link_value}`}
            className="block text-sm w-full whitespace-nowrap transition-colors hover:text-blue-600"
          >
            {link.name}
          </Link>
        );
      case "external":
        return (
          <a
            href={link.link_value}
            target="_blank"
            rel="noopener noreferrer"
            className="block text-sm w-full whitespace-nowrap transition-colors hover:text-blue-600"
          >
            {link.name}
          </a>
        );
      case "manual":
        return (
          <span className="block text-sm w-full whitespace-nowrap opacity-75">
            {link.name}
          </span>
        );
      default:
        return (
          <span className="block text-sm w-full whitespace-nowrap opacity-75">
            {link.name}
          </span>
        );
    }
  }, [parent]);

  return (
    <ul
      className={`absolute mt-1 bg-white shadow-lg rounded z-10 whitespace-nowrap w-40 py-1 ${className}`}
      role="menu"
    >
      {childrenLinks.map((child) => (
        <li
          key={child.id}
          className="px-4 py-2 hover:bg-gray-100 w-full border-b border-gray-100 last:border-b-0"
          role="menuitem"
        >
          {renderChildLink(child)}
        </li>
      ))}
    </ul>
  );
};

export default memo(MenuDropdown);