import React, { memo } from "react";
import { FaqAccordion } from ".";

function Faq({ sections }) {
  if (!sections || !sections.length) {
    return <div className="text-gray-600 text-base">No FAQs available.</div>;
  }

  return (
    <div className="w-full">
      {sections.map((section, idx) => {
        const faqs = section?.content ? JSON.parse(section.content) : [];

        if (!faqs.length) {
          return (
            <div key={idx} className="text-gray-600 text-base my-4">
              No FAQs found in this section.
            </div>
          );
        }

        return <FaqAccordion key={idx} faqs={faqs} />;
      })}
    </div>
  );
}

export default memo(Faq);
