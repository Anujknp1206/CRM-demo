import React, { memo, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link, useLocation, useParams } from "react-router-dom";
import { RiUserLine } from "@remixicon/react";
import { fetchAuthorDetail } from "../features/functions";
import { ProfileTemplate } from ".";
import { Blog, LoaderPage } from "../components";
import { RouteMap } from "../sections";

function AuthorProfile() {
  const { slug } = useParams();
  const dispatch = useDispatch();
  const location = useLocation();

  useEffect(() => {
    dispatch(fetchAuthorDetail(slug));
  }, [slug, dispatch]);

  const {
    user,
    latestBlogs,
    loading,
    error,
    imgUrl,
    adminImgUrl,
    approvedBlogCount,
  } = useSelector((state) => state.AuthorData);

  const backgroundImage = location.state?.backgroundImage || null;

  // if (loading || !user) return <LoaderPage isLoading={true} />;

  const { name, description, photo, joining_date, address, email } = user;

  // Header Section
  const renderHeader = () => (
    <div
      className={`w-screen h-72 relative overflow-hidden ${
        backgroundImage ? "" : "bg-gradient-to-r from-blue-500 to-cyan-400"
      }`}
      style={
        backgroundImage
          ? {
              backgroundImage: `url(${backgroundImage})`,
              backgroundSize: "cover",
              backgroundPosition: "center",
            }
          : {}
      }
    >
      <div className="relative z-10 h-72 flex flex-col justify-center items-center px-4 sm:px-6 md:px-12 lg:px-20 xl:px-24 text-center">
        <div className="flex flex-col items-center gap-4">
          {photo ? (
            <img
              src={`${adminImgUrl}/${photo}`}
              className="h-24 w-24 rounded-full border-4 border-white shadow-lg"
              alt="author-pic"
            />
          ) : (
            <div className="h-24 w-24 rounded-full border-4 border-white bg-white flex items-center justify-center shadow-lg">
              <RiUserLine size={48} className="text-blue-500" />
            </div>
          )}
          <h1
            className={`text-3xl sm:text-4xl md:text-5xl font-bold drop-shadow-lg ${
              backgroundImage ? "text-black" : "text-white"
            }`}
          >
            {name}
          </h1>
          <div className="w-full flex justify-center items-center pt-5">
            <RouteMap pathname={location.pathname} />
          </div>
        </div>
      </div>
    </div>
  );

  // Main Content Section
  const renderMainContent = () => (
    <div className="bg-white shadow-xl rounded-xl px-6 py-8">
      <h2 className="text-2xl font-bold mb-4">About {name}</h2>
      <div className="prose max-w-3xl w-full section-content">
        {description ? (
          <div dangerouslySetInnerHTML={{ __html: description }} />
        ) : (
          <p className="text-gray-600">No bio available yet.</p>
        )}
      </div>
    </div>
  );

  // Sidebar Section
  const renderSidebar = () => (
    <div className="bg-white shadow-lg rounded-lg p-4 border border-gray-200">
      <h3 className="text-xl font-bold mb-4">Quick Facts</h3>
      <ul className="space-y-3">
        <li className="flex justify-between border-b pb-2">
          <Link
            to={`/author/${slug}/blogs`}
            className="font-medium text-gray-700"
          >
            Posted Blogs
          </Link>
          <span className="text-gray-600">{approvedBlogCount}</span>
        </li>
      </ul>
    </div>
  );

  // Blog rendering function
  const renderBlog = (blog) => (
    <Blog
      key={blog.id}
      author={user.display_name}
      image={`${imgUrl}/${blog.banner}`}
      title={blog.title}
      description={blog.meta_description}
      link={blog.slug}
      date={blog.post_date}
    />
  );

  return (
    <ProfileTemplate
      loading={loading}
      error={error}
      header={renderHeader()}
      mainContent={renderMainContent()}
      sidebar={renderSidebar()}
      relatedItems={latestBlogs}
      relatedItemsTitle={`Latest Blogs by ${name}`}
      renderRelatedItem={renderBlog}
    />
  );
}

export default memo(AuthorProfile);
