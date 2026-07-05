import { lazy } from "react";

const Navbar = lazy(() => import("./Navbar"));
const ParentBlog = lazy(() => import("./ParentBlog"));
const SearchBox = lazy(() => import("./SearchBox"));
const Blogs = lazy(() => import("./Blogs"));
const Blog = lazy(() => import("./Blog"));
const LeftPanel = lazy(() => import("./LeftPanel"));
const RightPanel = lazy(() => import("./RightPanel"));
const Footer = lazy(() => import("./Footer"));
const Filters = lazy(() => import("./Filters"));
const FilterDropdown = lazy(() => import("./FilterDropdown"));
const Logo = lazy(() => import("./Logo"));
const NavLinks = lazy(() => import("./NavLinks"));
const NavMobileMenu = lazy(() => import("./NavMobileMenu"));
const MenuDropdown = lazy(() => import("./MenuDropdown"));
const NotFound = lazy(() => import("./NotFound"));
const MobileNavLinks = lazy(() => import("./MobileNavLinks"));
const ContactForm = lazy(() => import("./ContactForm"));
const ContactInfo = lazy(() => import("./ContactInfo"));
const Faq = lazy(() => import("./Faq"));
const FaqAccordion = lazy(() => import("./FaqAccordion"));
const LoaderPage = lazy(() => import("./LoaderPage"));
const PulseLoader = lazy(() => import("./PulseLoader"));
const ComapaiesList = lazy(() => import("./ComapaiesList"));
const Slider = lazy(() => import("./Slider"));
const HeroBlog = lazy(() => import("./HeroBlog"));
const BlogListItem = lazy(() => import("./BlogListItem"));
const LatestBlogsList = lazy(() => import("./LatestBlogsList"));
const FeatureBlogs = lazy(() => import("./FeatureBlogs"));

export {
  Navbar,
  ParentBlog,
  SearchBox,
  Blogs,
  Blog,
  LeftPanel,
  RightPanel,
  Footer,
  Filters,
  FilterDropdown,
  Logo,
  NavLinks,
  NavMobileMenu,
  MenuDropdown,
  NotFound,
  MobileNavLinks,
  ContactForm,
  ContactInfo,
  Faq,
  FaqAccordion,
  LoaderPage,
  PulseLoader,
  ComapaiesList,
  Slider,
  HeroBlog,
  BlogListItem,
  LatestBlogsList,
  FeatureBlogs,
};
