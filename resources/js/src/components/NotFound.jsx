import React from 'react';
import { Link } from 'react-router-dom';

const Error404 = () => {
  return (
    <div className="flex flex-col items-center justify-center py-20 px-4 bg-gradient-to-br from-gray-100 to-blue-50">
      <div className="bg-white shadow-md rounded-lg p-8 md:p-12 text-center max-w-xl w-full">
        <h1 className="text-6xl font-extrabold text-blue-600 mb-4">404</h1>
        <h2 className="text-2xl font-semibold mb-2">Page Not Found</h2>
        <p className="text-gray-600 mb-6">
          Sorry, the page you're looking for isn't here or has been moved.
        </p>
        <Link
          to="/"
          className="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-300"
        >
          ← Back to Home
        </Link>
      </div>
    </div>
  );
};

export default Error404;