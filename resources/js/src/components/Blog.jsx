import React, { memo } from "react";
import { RiCalendarLine } from "@remixicon/react";
import { Link } from "react-router-dom";

function Blog(props) {
  // Medium-intensity gradient combinations
  const gradients = [
    "from-purple-300 to-blue-400",
    "from-emerald-300 to-teal-400",
    "from-pink-300 to-purple-400",
    "from-blue-300 to-indigo-400",
    "from-cyan-300 to-sky-400",
  ];
  const randomGradient =
    gradients[Math.floor(Math.random() * gradients.length)];

  return (
    <div className="cursor-pointer col-span-1 flex flex-col gap-3 shadow-lg rounded-lg overflow-hidden bg-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full">
      {/* Image or Gradient Placeholder */}
      <div className="h-40 overflow-hidden">
        {props.image ? (
          <img
            src={props.image}
            alt={props.title}
            className="object-cover w-full h-full"
          />
        ) : (
          <div
            className={`w-full h-full bg-gradient-to-br ${randomGradient}`}
          />
        )}
      </div>

      {/* Content */}
      <div className="p-4 flex flex-col flex-1">
        <h3 className="font-bold text-lg text-gray-800 line-clamp-2 mb-2">
          {props.title}
        </h3>
        <p className="text-sm text-gray-600 line-clamp-2 mb-4">
          {props.description}
        </p>
        <div className="flex justify-between items-center mt-auto">
          <div className="flex flex-col gap-2">
            <Link className="text-sm text-blue-600 hover:underline hover:text-blue-800" to={`/author/${props.author}`}>
              @{props.author}
            </Link>
            <Link
              className="text-sm text-blue-600 hover:text-white font-medium px-1 rounded hover:bg-blue-600 transition-colors border border-blue-600"
              to={`/blogs/${props.link}`}
            >
              Read Article
            </Link>
          </div>
          <div className="flex items-center gap-2">
            <RiCalendarLine size={16} className="text-gray-500" />
            <span className="text-xs text-gray-500">{props.date}</span>
          </div>
        </div>
      </div>
    </div>
  );
}

export default memo(Blog);
