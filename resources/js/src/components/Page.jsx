import React, { memo, useEffect, useMemo, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchPages } from "../features/pageSlice";
import { ContactInfo, ContactForm, Faq, LoaderPage, NotFound } from ".";
import { BASE_URL } from "../utils/config";
import { useLocation } from "react-router-dom";

function Page({ slug }) {
  const dispatch = useDispatch();
  const location = useLocation();

  const { menuItems } = useSelector((state) => state.menuItems);
  const { pagesData, loading, error } = useSelector((state) => state.pagesData);

  const menuSlug = slug || location.pathname.split("/").pop();

  const [pageId, setPageId] = useState(null);

  useEffect(() => {
    if (menuItems.length > 0) {
      const matched = menuItems.find((item) => item.slug === menuSlug);
      if (matched) {
        setPageId(matched.link_value);
        dispatch(fetchPages(matched.link_value));
      }
    }
  }, [dispatch, menuItems, menuSlug]);

  const { title, sections = [] } = pagesData || {};

  const bannerSection = useMemo(
    () => sections.find((section) => section.type === "banner"),
    [sections]
  );

  const bannerContent = useMemo(() => {
    try {
      return bannerSection?.banner_content
        ? JSON.parse(bannerSection.banner_content)
        : {};
    } catch (err) {
      console.error("Failed to parse banner_content:", err);
      return {};
    }
  }, [bannerSection]);

  const hasImage = !!bannerContent.image;
  const hasBgColor = !!bannerContent.bg_color && bannerContent.bg_color.toLowerCase() !== "#ffffff";

  // 🛑 Important handling
  if (menuItems.length === 0) return <LoaderPage isLoading={true} />;
  if (!pageId) return <NotFound />;  // If no matching pageId found, show NotFound
  if (loading) return <LoaderPage isLoading={true} />;
  if (error) return <div className="text-red-500 text-sm">Failed to load page.</div>;
  if (!pagesData) return <div>No data available.</div>;

  return (
    <div className="page-container">
      {/* Banner Section */}
      <div className="w-full h-56 relative overflow-hidden">
        {(!hasImage && !hasBgColor) && (
          <div className="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-400" />
        )}

        {hasBgColor && (
          <div
            className="absolute inset-0"
            style={{ backgroundColor: bannerContent.bg_color }}
          />
        )}

        {hasImage && (
          <div
            className="absolute inset-0 bg-cover bg-center"
            style={{ backgroundImage: `url(${BASE_URL}/${bannerContent.image})` }}
          />
        )}

        {hasImage && (
          <div className="absolute bottom-0 w-full h-16 bg-gradient-to-t from-white to-transparent z-10" />
        )}

        <div className="relative z-20 h-56 flex flex-col justify-center items-center px-4 sm:px-6 md:px-12 lg:px-20 xl:px-24 text-center">
          <h1 className="text-4xl sm:text-5xl md:text-6xl font-bold drop-shadow-lg text-white capitalize">
            {title}
          </h1>
        </div>
      </div>

      {/* Page Content */}
      <div className="max-w-screen-xl mx-auto my-4 px-4 prose section-content">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
          {sections.map((section) => {
            switch (true) {
              case section.type === "content" && section.name === "Contact Info":
                return (
                  <div key={`contact-info-${section.id}`}>
                    <ContactInfo />
                  </div>
                );

              case section.type === "contact" && section.name === "Contact Form":
                return (
                  <div key={`contact-form-${section.id}`}>
                    <ContactForm />
                  </div>
                );

              case section.type === "faq":
                return (
                  <div className="col-span-2" key={`faq-${section.id}`}>
                    <Faq sections={[section]} />
                  </div>
                );

              default:
                return (
                  <div
                    key={section.id || Math.random()}
                    dangerouslySetInnerHTML={{ __html: section.content }}
                    className="md:col-span-2"
                  />
                );
            }
          })}
        </div>
      </div>
    </div>
  );
}

export default memo(Page);
