import React, { memo } from "react";
import { useSelector } from "react-redux";
import { Link } from "react-router-dom";

function Footer() {
  const { settingsData, error } = useSelector((state) => state.settingsData);

  if (error) {
    return <div className="text-red-500 text-center py-4">Error loading footer</div>;
  }

  const footerSections = [...(settingsData?.footerSections || [])];
  footerSections.sort((a, b) => (a.pivot?.order || 0) - (b.pivot?.order || 0));

  return (
    <footer className="bg-gray-900 text-gray-300 pt-8 pb-6 border-t border-gray-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {footerSections.map((section, index) => {
          const { type, footer_links, banner_content } = section;

          if (!banner_content || type !== "footer" || !footer_links) return null;

          return (
            <ul
              key={index}
              className="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-sm mb-6"
            >
              {JSON.parse(footer_links).map((link, linkIndex) => (
                <li key={linkIndex}>
                  <Link
                    to={`/${link.url.replace(/^\/+/, "")}`}
                    className="hover:text-white hover:underline transition-colors duration-200"
                  >
                    {link.title}
                  </Link>
                </li>
              ))}
            </ul>
          );
        })}

        <div className="border-t justify-centerborder-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
          <p className="text-center w-full">&copy; {new Date().getFullYear()} Find Anything. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}

export default memo(Footer);
