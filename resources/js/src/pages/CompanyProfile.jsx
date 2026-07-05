import React, { memo, useEffect, useState } from "react";
import {
  RiGlobalLine,
  RiMapPinLine,
  RiPhoneLine,
  RiMailLine,
  RiFacebookCircleLine,
  RiInstagramLine,
  RiTwitterXLine,
  RiLinkedinLine,
  RiStarFill,
  RiStarLine,
} from "@remixicon/react";
import { useLocation, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { fetchCompany } from "../features/functions";
import { Blog, LoaderPage } from "../components";
import { ProfileTemplate } from ".";
import { API_URL, BASE_IMG_URL } from "../utils/config";
import { RouteMap } from "../sections";
import axios from "axios";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

function CompanyProfile() {

  const [reviewForm, setReviewForm] = useState({
    fullname: "",
    email: "",
    contact: "",
    comment: "",
    company_id: "",
  });

  const { slug } = useParams();
  const dispatch = useDispatch();
  const location = useLocation();

  useEffect(() => {
    dispatch(fetchCompany(slug));
  }, [slug, dispatch]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setReviewForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleReviewSubmit = (e) => {
    e.preventDefault();
    setReviewForm((prev) => ({ ...prev, company_id: company.company.id }));
    axios
      .post(`${API_URL}/company-review`, reviewForm, {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((res) => {
        toast.success(
          "Review submitted successfully! after approval it will be visible publically",
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
        toast.error("Failed to submit review. Please try again.", {
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

  const { company, loading, error } = useSelector((state) => state.companies);


  if (loading || !company) return <LoaderPage isLoading={true} />;

  const socials = company.company.social_data || {};
  const latestBlogs = company.latest_blogs || [];

  // Render star rating
  const renderStars = (rating) => {
    return (
      <div className="flex items-center">
        {[1, 2, 3, 4, 5].map((star) =>
          star <= rating ? (
            <RiStarFill key={star} className="text-green-400" />
          ) : (
            <RiStarLine key={star} className="text-gray-400" />
          )
        )}
        <span className="ml-2 text-gray-600">{rating.toFixed(1)}</span>
      </div>
    );
  };

  // Header Section
  const renderHeader = () => (
    <div
      className="w-screen h-72 relative overflow-hidden"
      style={{
            backgroundImage: `url(${BASE_IMG_URL}/main-banner2.jpg)`,
            backgroundSize: "cover",
            backgroundPosition: "center",
        }}

    >
      <div className="relative z-10 h-72 flex flex-col justify-center items-center px-4 sm:px-6 md:px-12 lg:px-20 xl:px-24 text-center">
        <div className="flex flex-col items-center gap-4">
          <div className="h-24 w-24 rounded-full border-4 object-cover border-white bg-white flex items-center justify-center shadow-lg">
            <img
              src={`${BASE_IMG_URL}/company/logo/${company.company.logo}`}
              alt={company.company.name}
            />
          </div>
          <h1 className="text-3xl sm:text-4xl md:text-5xl font-bold text-dark drop-shadow-lg">
            {company.company.name}
          </h1>
          <div className="flex items-center gap-4">
            <div className="px-4 py-1 rounded-full bg-blue-600 bg-opacity-20 shadow-sm">
              <span className="text-sm text-white">
                {company.company.category.name}
              </span>
            </div>
          </div>
        </div>
        {/* <div className="w-full flex justify-center items-center pt-5">
              <RouteMap pathname={location.pathname} />
            </div> */}
      </div>
    </div>
  );

  // Main Content Section
  const renderMainContent = () => (
    <>
      {/* Company Description */}
      <div className="bg-white shadow-xl rounded-xl px-6">
        <img
                src={`${BASE_IMG_URL}/company/banner/${company.company.banner}`}
                alt="Company Banner"
                className="my-6 rounded-lg w-full object-cover"
                />
        <div
          className="prose max-w-3xl w-full section-content"
          dangerouslySetInnerHTML={{ __html: company.company.description }}
        />
      </div>

      {/* Comment Section */}
      <div className="mt-10 bg-slate-200 shadow-xl rounded-xl p-6">
        <h2 className="font-bold text-2xl mb-4">Customer Reviews</h2>

      </div>
      {/* End Comment Section */}

      <hr className="my-6 border-gray-300" />
      <div className="bg-slate-200 shadow-xl rounded-xl p-6">
        <form className="space-y-4" onSubmit={handleReviewSubmit}>
          <h2 className="font-bold text-3xl">Leave a review</h2>
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
              value={reviewForm.fullname}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            />
            <input
              type="email"
              name="email"
              placeholder="Email"
              value={reviewForm.email}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            />
          </div>
          <input
            type="number"
            name="contact"
            placeholder="Contact"
            value={reviewForm.contact}
            onChange={handleChange}
            required
            className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
          />
          <textarea
            name="comment"
            rows="4"
            value={reviewForm.comment}
            required
            placeholder="Your Comment"
            onChange={handleChange}
            className="border border-gray-300 rounded-md p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
          ></textarea>
          <button
            type="submit"
            className="bg-blue-600 text-white font-bold px-6 py-2 rounded-md hover:bg-blue-700"
          >
            Submit Review
          </button>
        </form>
      </div>
    </>
  );

  // Sidebar Section
  const renderSidebar = () => (
    <>
      {/* Company Details */}
    <div className="bg-white border border-gray-200 p-6">
        <h3 className="text-lg sm:text-xl font-semibold mb-4 text-gray-800">Company Details</h3>

        <ul className="space-y-4 text-sm sm:text-base">
            {/* Location */}
            {company.company.address && (
            <li className="flex items-start gap-3 border-b border-gray-100 pb-3">
                <RiMapPinLine className="text-blue-500 mt-1 flex-shrink-0" />
                <div>
                <p className="font-medium text-gray-700">Location</p>
                <p className="text-gray-600">{company.company.address}</p>
                </div>
            </li>
            )}

            {/* Blog Count */}
            <li className="flex items-center justify-between pt-1">
            <p className="font-medium text-gray-700 truncate">
                {company.company.name}&rsquo;s Blogs
            </p>
            <span className="text-gray-600">{company.total_blogs}</span>
            </li>
        </ul>
    </div>


      {/* Contact & Social Links Section */}
       <div className="bg-white border border-gray-200 p-6">
        <h3 className="text-lg sm:text-xl font-semibold mb-4 text-gray-800">Contact & Links</h3>

        <div className="space-y-5 text-sm sm:text-base">
            {/* Website */}
            {company.company.website && (
            <div className="flex items-start gap-3">
                <RiGlobalLine className="text-blue-500 mt-1 flex-shrink-0" />
                <div>
                <p className="font-medium text-gray-700">Website</p>
                <a
                    href={company.company.website}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-blue-600 cursor:pointer break-all"
                >
                    {company.company.website}
                </a>
                </div>
            </div>
            )}

            {/* Phone */}
            {company.company.contact && (
            <div className="flex items-start gap-3">
                <RiPhoneLine className="text-blue-500 mt-1 flex-shrink-0" />
                <div>
                <p className="font-medium text-gray-700">Support</p>
                {Object.entries(company.company.contact).map(([key, contactInfo]) => (
                    <div key={key}>
                    <a
                        href={`tel:${contactInfo.tfn_number}`}
                        className="text-gray-600 hover:text-blue-600 block"
                    >
                        {contactInfo.phone_number}
                    </a>
                    </div>
                ))}
                </div>
            </div>
            )}

            {/* Email */}
            {company.company.email && (
            <div className="flex items-start gap-3">
                <RiMailLine className="text-blue-500 mt-1 flex-shrink-0" />
                <div>
                <p className="font-medium text-gray-700">Email</p>
                <a
                    href={`mailto:${company.company.email}`}
                    className="text-gray-600 hover:text-blue-600 break-all"
                >
                    {company.company.email}
                </a>
                </div>
            </div>
            )}

            {/* Social Media */}
            {(socials.twitter || socials.facebook || socials.linkedin || socials.instagram) && (
            <div>
                <p className="font-medium text-gray-700 mb-2">Social Media</p>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                {socials.twitter && (
                    <a
                    href={socials.twitter}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-2 text-gray-600 hover:text-blue-400 transition-colors"
                    >
                    <RiTwitterXLine size={18} />
                    <span className="truncate">Twitter</span>
                    </a>
                )}
                {socials.facebook && (
                    <a
                    href={socials.facebook}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors"
                    >
                    <RiFacebookCircleLine size={18} />
                    <span className="truncate">Facebook</span>
                    </a>
                )}
                {socials.linkedin && (
                    <a
                    href={socials.linkedin}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-2 text-gray-600 hover:text-blue-700 transition-colors"
                    >
                    <RiLinkedinLine size={18} />
                    <span className="truncate">LinkedIn</span>
                    </a>
                )}
                {socials.instagram && (
                    <a
                    href={socials.instagram}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-2 text-gray-600 hover:text-pink-600 transition-colors"
                    >
                    <RiInstagramLine size={18} />
                    <span className="truncate">Instagram</span>
                    </a>
                )}
                </div>
            </div>
            )}
        </div>
        </div>

    </>
  );

  // Blog rendering function
  const renderBlog = (blog) => (
    <Blog
      key={blog.id}
      author={blog.user.display_name}
      image={`${BASE_IMG_URL}/blog/${blog.banner}`}
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
        relatedItems={latestBlogs}
        relatedItemsTitle={`Latest Blogs by ${company.company.name}`}
        renderRelatedItem={renderBlog}
      />
    </>
  );
}

export default memo(CompanyProfile);
