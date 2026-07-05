import React from "react";
import { Link } from "react-router-dom";

function RouteMap({ pathname = "" }) {
  const pathSegments = pathname.split("/").filter(Boolean);

  const buildPath = (index) => {
    return "/" + pathSegments.slice(0, index + 1).join("/");
  };

  // Function to truncate long segments on mobile
  const formatSegment = (segment) => {
    const cleanedSegment = segment.replace(/-/g, ' ');
    return (
      <>
        <span className="hidden sm:inline capitalize">{cleanedSegment}</span>
        <span className="sm:hidden capitalize">
          {cleanedSegment.length > 12
            ? `${cleanedSegment.substring(0, 10)}...`
            : cleanedSegment}
        </span>
      </>
    );
  };

  return (
    <div className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-auto">
      <nav className="flex justify-center items-center text-sm text-gray-600 cursor-default py-2 md:py-4" aria-label="Breadcrumb">
        <ol className="inline-flex items-center space-x-1 md:space-x-2 flex-nowrap whitespace-nowrap">
          <li className="inline-flex items-center">
            <Link
              to="/"
              className="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200"
            >
              <svg
                className="w-4 h-4 mr-1 md:mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
              </svg>
              <span className="hidden xs:inline">Home</span>
            </Link>
          </li>

          {pathSegments.map((segment, index) => (
            <li key={index} className="flex items-center">
              <svg
                className="w-4 h-4 md:w-5 md:h-5 text-gray-400 mx-1"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fillRule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clipRule="evenodd"
                />
              </svg>
              {index === pathSegments.length - 1 ? (
                <span className="text-gray-500">
                  {formatSegment(segment)}
                </span>
              ) : (
                <Link
                  to={buildPath(index) === "/author" ? "/blogs" : buildPath(index)}
                  className="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                >
                  {segment === "author" ? "Blogs" : formatSegment(segment)}
                </Link>
              )}
            </li>
          ))}
        </ol>
      </nav>
    </div>
  );
}

export default RouteMap;