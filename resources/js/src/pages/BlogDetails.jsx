import React, { memo, useEffect, useState } from "react";
import { Link, useLocation, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { RiUserLine } from "@remixicon/react";
import { fetchBlogDetail } from "../features/functions";
import { Blog, LatestBlogsList, LoaderPage } from "../components";
import { ProfileTemplate } from ".";
import Filters from "../components/Filters";
import { API_URL, BASE_IMG_URL } from "../utils/config";
import axios from "axios";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { RouteMap } from "../sections";

function BlogDetails() {
  const [commentForm, setCommentForm] = useState({
    fullname: "",
    email: "",
    contact: "",
    comment: "",
    blog_id: "",
  });

  const [comments, setComments] = useState([]);
  const { slug } = useParams();
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchBlogDetail(slug));
  }, [slug, dispatch]);

  const { blog, relatedBlogs, latestBlogs, imgUrl, loading, error } =
    useSelector((state) => state.blogsData);

  useEffect(() => {
    if (blog?.id) {
      setCommentForm((prev) => ({ ...prev, blog_id: blog.id }));
    }
  }, [blog?.id]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setCommentForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleCommentSubmit = (e) => {
    e.preventDefault();
    axios
      .post(`${API_URL}/blog-comment`, commentForm, {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((res) => {
        toast.success(
          "Comment submitted successfully! after approval it will be visible publically",
          {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
          }
        );
        formReset();
        // Optionally fetch comments again to show the new one
        // fetchComments();
      })
      .catch((err) => {
        toast.error("Failed to submit comment. Please try again.", {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: false,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
        });
        console.log(err);
      });
  };

  const formReset = () => {
    setCommentForm({
      fullname: "",
      email: "",
      contact: "",
      comment: "",
      blog_id: "",
    });
  };

  if (loading || !blog || slug !== blog.slug)
    return <LoaderPage isLoading={true} />;

  const backgroundImage = `${imgUrl}/${blog.banner}`;
  const banner = `${BASE_IMG_URL}` + "main-banner.jpg";
  const bannerImage = blog?.banner
      ? `${BASE_IMG_URL}/blog/${blog.banner}`
      : `${BASE_IMG_URL}/no-image.png`;

  const location = useLocation();

  const renderHeader = () => (
    <>
      <div className="w-screen h-64 relative overflow-hidden">
        <div
          className="absolute inset-0 bg-cover bg-center bg-gradient-to-br from-purple-300 to-blue-400"
          style={{ backgroundImage: `url(${banner})` }}
        >
          <div className="absolute bottom-0 w-full h-24 bg-gradient-to-t from-white to-transparent" />
        </div>
        <div className="relative z-10 h-72 flex flex-col justify-start items-center pt-8 sm:pt-12 md:pt-16 px-4 sm:px-6 md:px-12 lg:px-20 xl:px-24 text-center">
          <h1 className="text-4xl sm:text-5xl md:text-6xl font-bold drop-shadow-lg capitalize">
            {blog.topic}
          </h1>
          <p className="pt-5 capitalize">({blog.tag})</p>
        </div>
      </div>
      <div className="w-full pb-3 flex justify-center items-center gap-4 capitalize">
        <Link
          to={`/author/${blog.user.display_name}`}
          state={{ backgroundImage: backgroundImage }}
          className="flex justify-center items-center gap-2"
        >
          {blog.user.photo ? (
            <img
              src={`${BASE_IMG_URL}user/${blog.user.photo}`}
              className="h-10 w-10 rounded-full border-cyan-700 border-2 cursor-pointer"
              alt="author-pic"
            />
          ) : (
            <span className="h-8 w-8 p-1 rounded-full text-blue-500 border-blue-500 border-2 cursor-pointer flex justify-center items-center">
              <RiUserLine />
            </span>
          )}
          <span className="underline text-blue-500 cursor-pointer">
            {blog.user.name}
          </span>
        </Link>
      </div>
      <div className="w-full flex justify-center items-center text-sm text-gray-500 font-bold">
        Posted: {blog.post_date}
      </div>
      <div className="w-full flex justify-center items-center pt-5">
        <RouteMap pathname={location.pathname} />
      </div>
    </>
  );

  const renderMainContent = () => (

    <>
      <div className="flex flex-wrap items-center gap-3 pb-4 mb-6 border-b border-gray-200">
        <div className="flex items-center gap-1.5">
          <Link className="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-colors duration-200">
            {blog.topic}
          </Link>
          <span className="text-gray-400">|</span>
        </div>
        <div className="flex items-center gap-1.5">
          <Link
            to={`/categories/${
              blog.category.slug ||
              blog.category.name.toLowerCase().replace(/\s+/g, "-")
            }`}
            className="px-2.5 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200 transition-colors duration-200"
          >
            {blog.category.name}
          </Link>
          <span className="text-gray-400">|</span>
        </div>
        <div className="flex flex-wrap items-center gap-1.5">
          {blog.tag.split(",").map((tag, index) => (
            <Link
              key={index}
              to={`/tags/${tag.trim().toLowerCase().replace(/\s+/g, "-")}`}
              className="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full hover:bg-green-200 transition-colors duration-200"
            >
              {tag.trim()}
            </Link>
          ))}
        </div>
      </div>
      <div className="relative z-10 pb-1 flex flex-col justify-start items-center pt-4">
        <h1 className="text-4xl sm:text-4xl md:text-4xl font-bold drop-shadow-lg pb-2 capitalize">
          {blog.title}
        </h1>
        <p className="pt-3 pl-4 capitalize text-xl">{blog.short_description}</p>
      </div>
        <div className="flex justify-center">
            <img
                src={bannerImage}
                alt="Blog"
                className="w-full max-w-[900px] h-auto aspect-[9/5] object-cover"
            />
        </div>

      <div className="bg-white shadow-xl rounded-xl px-4 py-1 w-full flex justify-center">
        <div
          className="prose sm:prose-sm lg:prose-lg xl:prose-xl max-w-3xl w-full section-content"
          dangerouslySetInnerHTML={{ __html: blog.description }}
        />
      </div>
      <div className="mt-8">
        <div className="flex items-center justify-between">
          <h2 className="text-xl font-bold">Comments</h2>
          <span className="text-sm font-bold text-gray-600 pr-2">
            ({blog.comments.length})
          </span>
        </div>

        <hr className="my-2" />
        <div className="space-y-4">
          {comments.map((comment) => (
            <div
              key={comment.id}
              className="border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 bg-white hover:bg-gray-50"
            >
              <h4 className="font-semibold">{comment.full_name}</h4>
              <p className="text-gray-700 text-sm mt-1">{comment.comment}</p>
            </div>
          ))}
        </div>

        {/* Comment Section */}
        <div className="mt-10 bg-slate-200 shadow-xl rounded-xl p-6">
          <h2 className="font-bold text-2xl mb-4">Blog Comments</h2>

          <div className="max-h-96 overflow-y-auto space-y-6 pr-2">
            {blog.comments && blog.comments.length > 0 ? (
              blog.comments.map((commentBlog) => (
                <div
                  key={commentBlog.id}
                  className="bg-white p-5 rounded-lg shadow border border-gray-300"
                >
                  <div className="flex justify-between items-center mb-1">
                    <div>
                      <h4 className="font-semibold text-gray-800 text-base">
                        {commentBlog.full_name}
                      </h4>
                      <span className="text-xs text-gray-500">
                        {new Date(commentBlog.created_at).toLocaleString(
                          "en-US",
                          {
                            dateStyle: "long",
                            timeStyle: "short",
                          }
                        )}
                      </span>
                    </div>
                  </div>

                  <p className="text-gray-700 text-sm leading-relaxed mb-4">
                    {commentBlog.comment}
                  </p>

                  {/*<div className="pt-2 border-t border-gray-200 mt-2">
                      <Link
                        to=""
                        className="inline-flex items-center gap-2 text-sm text-blue-600 border border-blue-600 px-4 py-2 rounded-full font-semibold hover:bg-blue-600 hover:text-white transition-all duration-200"
                      >
                        Reply to this comment
                      </Link>
                    </div>*/}
                </div>
              ))
            ) : (
              <p className="text-md text-gray-600">
                No comments yet. Be the first to comment!
              </p>
            )}
          </div>
        </div>
        {/* End Comment Section */}

        <hr className="my-6 border-gray-300" />
        <div className="bg-slate-200 shadow-xl rounded-xl p-6">
          <form className="space-y-4" onSubmit={handleCommentSubmit}>
            <h2 className="font-bold text-3xl">Leave a comment</h2>
            <p className="text-md text-gray-600">
              Provide clear contact information, including phone number, email,
              and address.
            </p>
            <hr className="border-gray-300" />
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <input
                type="text"
                name="fullname"
                placeholder="Full Name"
                value={commentForm.fullname}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
              />
              <input
                type="email"
                name="email"
                placeholder="Email"
                value={commentForm.email}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
              />
            </div>
            <input
              type="number"
              name="contact"
              placeholder="Contact"
              value={commentForm.contact}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            />
            <textarea
              name="comment"
              rows="4"
              value={commentForm.comment}
              required
              placeholder="Your Comment"
              onChange={handleChange}
              className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            ></textarea>
            <button
              type="submit"
              className="bg-blue-600 text-white font-bold px-6 py-2 rounded-md hover:bg-blue-700"
            >
              Submit Comment
            </button>
          </form>
        </div>
      </div>
    </>
  );

  const renderSidebar = () => (
    <>
      <div className="bg-white">
        <Filters />
      </div>
      <LatestBlogsList />
      {/* <div className="bg-white shadow-lg rounded-lg p-4 border border-gray-200">
        <h3 className="text-xl font-bold mb-4">Latest Blogs</h3>
        <ul className="space-y-3">
          {latestBlogs.map((item) => (
            <li key={item.id} className="border-b pb-2">
              <Link
                to={`/blogs/${item.slug}`}
                className="text-blue-600 font-medium hover:underline"
              >
                {item.title}
              </Link>
              <div className="text-xs text-gray-500">{item.post_date}</div>
            </li>
          ))}
        </ul>
      </div> */}
    </>
  );

  const renderBlog = (blog) => (
    <Blog
      key={blog.id}
      author={blog.user.display_name}
      image={`${BASE_IMG_URL}blog/${blog.banner}`}
      title={blog.title}
      description={blog.meta_description}
      link={blog.slug}
      date={blog.post_date}
    />
  );

  return (
    <>
      <ToastContainer />
      <ProfileTemplate
        loading={loading}
        error={error}
        header={renderHeader()}
        mainContent={renderMainContent()}
        sidebar={renderSidebar()}
        relatedItems={relatedBlogs}
        relatedItemsTitle="Related Blogs"
        renderRelatedItem={renderBlog}
      />
    </>
  );
}

export default memo(BlogDetails);
