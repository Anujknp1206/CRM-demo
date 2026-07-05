import { lazy } from "react";

const AuthorProfile = lazy(() => import("./AuthorProfile"));
const CompanyProfile = lazy(() => import("./CompanyProfile"));
const BlogDetails = lazy(() => import("./BlogDetails"));
const HomePage = lazy(() => import("./HomePage"));
const ProfileTemplate = lazy(() => import("./ProfileTemplate"));
const Page = lazy(() => import("./Page"));

export {
  AuthorProfile,
  CompanyProfile,
  BlogDetails,
  HomePage,
  ProfileTemplate,
  Page,
};
