import React, { memo, useState, useEffect } from "react";
import {
  RiArrowDropDownLine,
  RiArrowDropRightLine,
  RiArrowDropUpLine,
} from "@remixicon/react";

function FilterDropdown({ name, options = [], selected = [], onChange }) {
  const [open, setOpen] = useState(true);
  const [checked, setChecked] = useState(selected || []);
  const [showAll, setShowAll] = useState(false);

  useEffect(() => {
    setChecked(selected || []);
  }, [selected]);

  const toggleOption = (id) => {
    const newChecked = checked.includes(id)
      ? checked.filter((item) => item !== id)
      : [...checked, id];
    setChecked(newChecked);
    onChange(name.toLowerCase(), newChecked);
  };

  const visibleOptions = showAll ? options : options.slice(0, 5);

  return (
    <div className="mb-4 border border-gray-200 rounded-md bg-white shadow-sm">
      {/* Header */}
      <div
        className="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-100 rounded-t-md"
        onClick={() => setOpen(!open)}
      >
        <div className="flex items-center space-x-2">
          {open ? (
            <RiArrowDropDownLine size={28} />
          ) : (
            <RiArrowDropRightLine size={28} />
          )}
          <h3 className="font-semibold text-lg">Blogs by {name}</h3>
        </div>
      </div>

      {/* Filter options */}
      {open && (
        <div className="px-4 py-3">
          {options.length > 0 ? (
            <>
              <ul className="space-y-2">
                {visibleOptions.map((option) => {
                  const id = `${name}-${option.id}`;
                  return (
                    <li key={id} className="flex items-center text-sm">
                      <input
                        type="checkbox"
                        id={id}
                        checked={checked.includes(option.id)}
                        onChange={() => toggleOption(option.id)}
                        className="mr-2 accent-blue-600"
                      />
                      <label htmlFor={id} className="cursor-pointer text-base">
                        {option.label}
                      </label>
                    </li>
                  );
                })}
              </ul>

              {/* See More/Less toggle */}
              {options.length > 5 && (
                <div
                  onClick={() => setShowAll(!showAll)}
                  className="flex items-center gap-1 text-blue-600 text-sm mt-3 cursor-pointer hover:underline w-fit"
                >
                  <span>{showAll ? "See Less" : "See More"}</span>
                  {showAll ? (
                    <RiArrowDropUpLine size={20} />
                  ) : (
                    <RiArrowDropDownLine size={20} />
                  )}
                </div>
              )}
            </>
          ) : (
            <p className="text-gray-400 text-sm">No options available</p>
          )}
        </div>
      )}
    </div>
  );
}

export default memo(FilterDropdown);
