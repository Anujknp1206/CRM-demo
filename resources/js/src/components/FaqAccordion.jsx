import React, { useState, memo } from "react";

function FaqAccordion({ faqs }) {
  const [openIndex, setOpenIndex] = useState(null);

  if (!faqs?.length) return null;

  const toggle = (index) => {
    setOpenIndex(openIndex === index ? null : index);
  };

  return (
    <div className="w-full mx-auto my-3 px-4">
      {faqs.map((faq, index) => (
        <div key={index} className="border-b border-gray-300">
          <h2>
            <button
              className="w-full text-left py-1 flex justify-between items-center focus:outline-none cursor-pointer"
              onClick={() => toggle(index)}
            >
              <span className="text-lg font-semibold text-gray-800">
                {faq.question}
              </span>
              <span className="text-2xl font-bold text-gray-600">
                {openIndex === index ? "−" : "+"}
              </span>
            </button>
          </h2>
          <div
            className={`transition-all duration-500 ease-in-out overflow-hidden ${
              openIndex === index
                ? "max-h-screen opacity-100"
                : "max-h-0 opacity-0"
            }`}
          >
            {openIndex === index && (
              <div className="pb-5 text-gray-700 text-base leading-relaxed whitespace-pre-line">
                {faq.answer}
              </div>
            )}
          </div>
        </div>
      ))}
    </div>
  );
}

export default memo(FaqAccordion);
