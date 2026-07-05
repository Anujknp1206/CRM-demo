import { lazy } from "react";

const HeroSection = lazy(() => import("./HeroSection"));
const FeaturePills = lazy(() => import("./FeaturePills"));
const SliderContainer = lazy(() => import("./SliderContainer"));
const Banner = lazy(() => import("./Banner"));
const FeatureBlogsContainer = lazy(() => import("./FeatureBlogsContainer"));
const RouteMap = lazy(() => import("./RouteMap"));

export { HeroSection, FeaturePills, SliderContainer, Banner, FeatureBlogsContainer, RouteMap };