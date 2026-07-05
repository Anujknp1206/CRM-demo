import React, { memo, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link, useLocation } from "react-router-dom";
import {
  RiGlobalLine,
  RiTwitterXLine,
  RiFacebookCircleLine,
  RiLinkedinLine,
  RiInstagramLine,
} from "@remixicon/react";
// import { LoaderPage } from ".";
import { BASE_IMG_URL } from "../utils/config";
import { fetchCompanies } from "../features/functions";
import Banner from "../sections/Banner";
import { RouteMap } from "../sections";

function CompanyList() {
  const dispatch = useDispatch();
  const location = useLocation();
  const { companies, loading, error, total, currentPage, totalPages } =
    useSelector((state) => state.companies);

  useEffect(() => {
    dispatch(fetchCompanies(1));
  }, [dispatch]);

  const handleShowMore = () => {
    if (currentPage < totalPages) {
      dispatch(fetchCompanies(currentPage + 1));
    }
  };

  // if (loading && companies.length === 0) return <LoaderPage isLoading={true} />;
  if (error)
    return <div className="text-red-600 text-center py-10">Error: {error}</div>;

  return (
    <div className="w-full mx-auto relative min-h-96">
      {/* Banner Section */}
      <Banner
        heading="Discover Top Companies"
        para="Explore companies from around the world"
      >
        <RouteMap pathname={location.pathname} />
      </Banner>

      {/* Main Content */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-4 px-2 sm:px-4 md:px-6 lg:px-8 py-8">
        {/* Companies Grid */}
        <div className="md:col-span-12">
          {/* Info Bar */}
          <div className="mb-8 bg-white shadow-md rounded-lg border border-gray-200 p-4">
            <div className="text-sm text-gray-600">
              Showing {companies.length} of {total} companies
            </div>
          </div>

          {/* Companies Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {companies.map((company) => {
              const socials = company.social_data;

              return (
                <div
                  key={company.slug}
                  className="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-200 hover:border-blue-300"
                >
                  <Link to={`/company/${company.slug}`} className="block">
                    {/* Logo */}
                    <div className="h-40 bg-gray-100 flex items-center justify-center p-4">
                      {company.logo ? (
                        <img
                          src={`${BASE_IMG_URL}/company/logo/${company.logo}`}
                          alt={`${company.name} logo`}
                          className="max-h-full max-w-full object-cover"
                        />
                      ) : (
                        <div className="text-4xl font-bold text-gray-400">
                          {company.name.charAt(0).toUpperCase()}
                        </div>
                      )}
                    </div>

                    {/* Info */}
                    <div className="p-5">
                      <h3 className="text-xl font-bold mb-2 line-clamp-1 hover:text-blue-600">
                        {company.name}
                      </h3>
                      <p className="text-gray-600 mb-4 line-clamp-3 h-[4.5rem]">
                        {company.short_description}
                      </p>
                    </div>
                  </Link>

                  {/* External Links (NOT inside Link) */}
                  <div className="px-5 pb-5">
                    {company.website && (
                      <div className="flex items-center">
                        <RiGlobalLine className="text-blue-500 mr-2" />
                        <a
                          href={company.website}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-600 hover:underline text-sm truncate"
                        >
                          {company.website.replace(/^https?:\/\//, "")}
                        </a>
                      </div>
                    )}

                    <div className="flex space-x-3 mt-2">
                      {socials.twitter && (
                        <a
                          href={socials.twitter}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-400 hover:text-blue-600"
                          onClick={(e) => e.stopPropagation()}
                        >
                          <RiTwitterXLine size={18} />
                        </a>
                      )}
                      {socials.facebook && (
                        <a
                          href={socials.facebook}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-600 hover:text-blue-800"
                          onClick={(e) => e.stopPropagation()}
                        >
                          <RiFacebookCircleLine size={18} />
                        </a>
                      )}
                      {socials.linkedin && (
                        <a
                          href={socials.linkedin}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-700 hover:text-blue-900"
                          onClick={(e) => e.stopPropagation()}
                        >
                          <RiLinkedinLine size={18} />
                        </a>
                      )}
                      {socials.instagram && (
                        <a
                          href={socials.instagram}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-pink-600 hover:text-pink-800"
                          onClick={(e) => e.stopPropagation()}
                        >
                          <RiInstagramLine size={18} />
                        </a>
                      )}
                    </div>
                  </div>
                </div>
              );
            })}
          </div>

          {/* Show More Button */}
          {currentPage < totalPages && (
            <div className="mt-10 flex justify-center">
              <button
                onClick={handleShowMore}
                disabled={loading}
                className="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 disabled:opacity-50"
              >
                {loading ? "Loading..." : "Show More"}
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default memo(CompanyList);
