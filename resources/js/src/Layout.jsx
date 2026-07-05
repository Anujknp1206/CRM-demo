import React from "react";
import { Footer, LoaderPage, Navbar } from "./components";
import { Outlet } from "react-router-dom";
import { useSelector } from "react-redux";
import { selectIsLoading } from "./features/selectors/loadingSelectors";
import { preventCopyText } from "./utils/functions";

function Layout() {
  const isLoading = useSelector(selectIsLoading);

  return (
    <div onCopy={preventCopyText}>
      {/* <LoaderPage isLoading={isLoading} /> */}
      <Navbar />
      <Outlet />
      <Footer />
    </div>
  );
}

export default Layout;
