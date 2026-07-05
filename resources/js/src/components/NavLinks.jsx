import React, { memo, useState } from "react";
import { Link } from "react-router-dom";
import { RiArrowDropDownLine } from "@remixicon/react";
import { MenuDropdown } from ".";

function NavLinks({ navLinks = [], classNameLi = "" }) {
  const [activeDropdown, setActiveDropdown] = useState(null);
  const [timeoutId, setTimeoutId] = useState(null);

  const childrenMap = navLinks.reduce((acc, link) => {
    if (link.parent_id) {
      acc[link.parent_id] = acc[link.parent_id] || [];
      acc[link.parent_id].push(link);
    }
    return acc;
  }, {});

  const topLevelLinks = navLinks
    .filter(link => parseInt(link.order) === parseFloat(link.order) && link.parent_id === null)
    .sort((a, b) => a.order - b.order);

  const handleMouseEnter = (id) => {
    clearTimeout(timeoutId);
    setActiveDropdown(id);
  };

  const handleMouseLeave = () => {
    const id = setTimeout(() => {
      setActiveDropdown(null);
    }, 200);
    setTimeoutId(id);
  };

  const renderLink = (link) => {
    switch (link.link_type) {
      case "page":
        return (
          <Link
            to={`/${link.slug}`}
            className="cursor-pointer"
          >
            {link.name}
          </Link>
        );
      case "route":
        return (
          <Link
            to={link.link_value === "index" ? "/" : `/${link.link_value}`}
            className="cursor-pointer"
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
            className="cursor-pointer"
          >
            {link.name}
          </a>
        );
      case "manual":
        return (
          <span className="">
            {link.name}
          </span>
        );
      default:
        return (
          <span className="cursor-default">
            {link.name}
          </span>
        );
    }
  };

  return (
    <ul className={`flex flex-col md:flex-row gap-5 md:gap-8 items-start md:items-center`}>
      {topLevelLinks.map((link) => {
        const children = childrenMap[link.id] || [];
        const hasChildren = children.length > 0;

        return (
          <li
            key={link.id}
            className={`relative ${classNameLi}`}
            onMouseEnter={() => handleMouseEnter(link.id)}
            onMouseLeave={handleMouseLeave}
          >
            <div className="flex items-center font-bold text-sm">
              {hasChildren ? (
                <>
                  <span className="cursor-pointer">{link.name}</span>
                  <RiArrowDropDownLine className="text-lg ml-1" />
                </>
              ) : (
                renderLink(link)
              )}
            </div>

            {hasChildren && activeDropdown === link.id && (
              <div className="absolute top-5 left-0 mt-2 z-10">
                <MenuDropdown parent={link.slug} childrenLinks={children} />
              </div>
            )}
          </li>
        );
      })}
    </ul>
  );
}

export default memo(NavLinks);
