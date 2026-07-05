import React, { memo } from "react";
import { FeatureBlogsContainer, SliderContainer } from "../sections";

function HomePage() {
  return (
    <div className="w-full overflow-x-hidden">

      {/* Featured Blogs By Catagories */}
      <FeatureBlogsContainer />

      {/* Companies Slider Section - Full Width */}
      <SliderContainer
        heading="Trusted By Industry Leaders"
        para="We collaborate with top companies worldwide"
      />

    </div>
  );
}

export default memo(HomePage);
