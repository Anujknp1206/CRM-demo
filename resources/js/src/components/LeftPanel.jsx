import React, { memo } from "react";
import { Filters, SearchBox } from ".";

function LeftPanel() {
  return (
    <div className="md:col-span-3">
      <Filters />
    </div>
  );
}

export default memo(LeftPanel);
