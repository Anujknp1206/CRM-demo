import "./App.css";
import { Routes, Route } from "react-router-dom";
import Layout from "./Layout";
import { fetchMenuItems, fetchSettings } from "./features/functions";
import { AuthorProfile, CompanyProfile, BlogDetails, HomePage, Page } from "./pages";
import { ParentBlog, ComapaiesList, LoaderPage } from "./components";
import { useFetchThunk } from "./hooks/useFetchThunk";
import { memo, Suspense, useMemo } from "react";

function App() {
  const fetchConfigs = useMemo(
    () => [{ thunk: fetchMenuItems }, { thunk: fetchSettings }],
    []
  );

  useFetchThunk(fetchConfigs);

  return (
    <Suspense fallback={<LoaderPage isLoading={true} />}>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<HomePage />} />
          <Route path="blogs" element={<ParentBlog />} />
          <Route path="blogs/:slug" element={<BlogDetails />} />
          <Route path="companies" element={<ComapaiesList />} />
          <Route path="company/:slug" element={<CompanyProfile />} />
          <Route path="author/:slug" element={<AuthorProfile />} />
          {/* <Route path="category/:category" element={<ParentBlog />} />
          <Route path="tag/:tag" element={<ParentBlog />} />
          <Route path="topic/:topic" element={<ParentBlog />} /> */}
          <Route path="*" element={<Page />} />
        </Route>
      </Routes>
    </Suspense>
  );
}

export default memo(App);

{
  /* Static Pages - Footer Links */
}

// const { settingsData } = useSelector((state) => state.settingsData);

// const footerLinks =
// settingsData?.footerSections
//   ?.filter((section) => section.type === "footer" && section.footer_links)
//   .flatMap((section) => {
//     try {
//       return JSON.parse(section.footer_links);
//     } catch (e) {
//       console.error("Invalid footer_links JSON", e);
//       return [];
//     }
//   }) || [];

{
  /* {footerLinks.map((link, i) => (
  <Route
    key={`footer-link-${i}`}
    path={link.url.replace(/^\/+/, "")}
    element={<Page slug={link.url.split("/").pop()} />}
  />
))} */
}
