import React, { memo } from "react";
import { LoaderPage, NotFound, Blog } from "../components";

function ProfileTemplate({
  loading,
  error,
  header,
  mainContent,
  sidebar,
  relatedItems = [],
  relatedItemsTitle = "Related Items",
  renderRelatedItem = (item) => <div key={item.id}>{item.title}</div>
}) {
  // if (loading) return <LoaderPage isLoading={true} />;
  if (error) return <NotFound />;

  return (
    <div className="w-full mx-auto relative min-h-96">
      {/* Header Section */}
      {header}

      {/* Content Grid */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-4 px-2 sm:px-4 md:px-6 lg:px-8 py-8">
        {/* Main Content */}
        <div className="md:col-span-9 space-y-4">
          {mainContent}
        </div>

        {/* Sidebar */}
        <div className="md:col-span-3">
          <div className="sticky top-16 space-y-4">
            {sidebar}
          </div>
        </div>
      </div>

      {/* Related Items Section */}
      {relatedItems.length > 0 && (
        <div className="w-full px-2 sm:px-4 md:px-6 lg:px-8 py-8">
          <div className="bg-white shadow-xl rounded-xl p-6">
            <h2 className="text-2xl font-bold mb-6">{relatedItemsTitle}</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              {relatedItems.map(renderRelatedItem)}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default memo(ProfileTemplate);
