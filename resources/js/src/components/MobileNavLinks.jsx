import React, { memo, useState, useCallback } from "react";
import { Link } from "react-router-dom";
import {
  RiAddLine,
  RiArrowDropRightLine,
  RiSubtractLine,
} from "@remixicon/react";

/**
 * Mobile navigation links component with expandable dropdowns
 */
const MobileNavLinks = ({
  navLinks = [],
  classNameUl = "",
  classNameLi = "",
  onClick,
}) => {
  // State to track open dropdowns
  const [openDropdowns, setOpenDropdowns] = useState({});

  // Toggle dropdown visibility
  const toggleDropdown = useCallback((id) => {
    setOpenDropdowns((prev) => ({
      ...prev,
      [id]: !prev[id],
    }));
  }, []);

  // Organize links into parent-child relationship
  const childrenMap = navLinks.reduce((acc, link) => {
    if (link.parent_id) {
      acc[link.parent_id] = acc[link.parent_id] || [];
      acc[link.parent_id].push(link);
    }
    return acc;
  }, {});

  // Filter top-level links and sort by order
  const topLevelLinks = navLinks
    .filter((link) => parseInt(link.order) === parseFloat(link.order) && link.parent_id === null)
    .sort((a, b) => a.order - b.order);

  // Render link based on link_type
  const renderLink = useCallback((link, onClick = () => {}) => {
    switch (link.link_type) {
      case "page":
        return (
          <Link
            to={link.slug === "home" ? "/" : `/page/${link.link_value}`}
            className="cursor-pointer font-bold"
            onClick={onClick}
          >
            {link.name}
          </Link>
        );
      case "route":
        return (
          <Link
            to={link.link_value === "index" ? "/" : `/${link.link_value}`}
            className="cursor-pointer font-bold"
            onClick={onClick}
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
            className="cursor-pointer font-bold"
            onClick={onClick}
          >
            {link.name}
          </a>
        );
      case "manual":
        return <span className="font-bold cursor-not-allowed opacity-75">{link.name}</span>;
      default:
        return <span className="cursor-default font-bold">{link.name}</span>;
    }
  }, []);

  return (
    <ul className={`text-black md:hidden w-full ${classNameUl}`}>
      {topLevelLinks.map((link) => {
        const children = childrenMap[link.id] || [];
        const hasChildren = children.length > 0;
        const isOpen = openDropdowns[link.id];

        return (
          <li
            key={link.id}
            className={`relative group hover:bg-gray-200 py-5 border-b w-full p-5 border-gray-300 ${classNameLi}`}
          >
            <div
              className="flex items-center justify-between"
              onClick={(e) => {
                if (hasChildren) {
                  e.preventDefault();
                  toggleDropdown(link.id);
                } else {
                  onClick?.();
                }
              }}
            >
              {renderLink(link, (e) => {
                if (hasChildren) {
                  e.preventDefault();
                } else {
                  onClick?.();
                }
              })}

              {hasChildren && (
                <button
                  className="focus:outline-none focus:bg-gray-300 p-1 rounded-full"
                  aria-expanded={isOpen}
                  aria-label={`Expand ${link.name} submenu`}
                >
                  {isOpen ? (
                    <RiSubtractLine className="text-lg cursor-pointer" />
                  ) : (
                    <RiAddLine className="text-lg cursor-pointer" />
                  )}
                </button>
              )}
            </div>

            {hasChildren && isOpen && (
              <div>
                <MenuDropdown childrenLinks={children} onClose={onClick} />
              </div>
            )}
          </li>
        );
      })}
    </ul>
  );
};

/**
 * Dropdown menu for mobile navigation
 */
const MenuDropdown = ({ childrenLinks = [], className = "", onClose }) => {
  const renderLink = (link) => {
    switch (link.link_type) {
      case "page":
        return `/page/${link.link_value}`;
      case "route":
        return link.link_value === "index" ? "/" : `/${link.link_value}`;
      case "external":
        return link.link_value;
      default:
        return "#";
    }
  };

  return (
    <ul className={`pl-4 mt-2 border-l-2 border-gray-300 ${className}`}>
      {childrenLinks.map((child) => (
        <li key={child.id} className="py-3 hover:bg-gray-100 rounded">
          {child.link_type === "external" ? (
            <a
              href={renderLink(child)}
              target="_blank"
              rel="noopener noreferrer"
              className="text-sm w-full whitespace-nowrap font-bold flex items-center"
              onClick={onClose}
            >
              <RiArrowDropRightLine className="flex-shrink-0" />
              <span className="ml-1">{child.name}</span>
            </a>
          ) : child.link_type === "manual" ? (
            <span className="text-sm font-bold opacity-50 flex items-center">
              <RiArrowDropRightLine className="flex-shrink-0" />
              <span className="ml-1">{child.name}</span>
            </span>
          ) : (
            <Link
              to={renderLink(child)}
              className="text-sm w-full whitespace-nowrap font-bold flex items-center"
              onClick={onClose}
            >
              <RiArrowDropRightLine className="flex-shrink-0" />
              <span className="ml-1">{child.name}</span>
            </Link>
          )}
        </li>
      ))}
    </ul>
  );
};

export default memo(MobileNavLinks);