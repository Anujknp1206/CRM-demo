import React, { memo, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchSettings } from "../features/functions";
import { API_URL, BASE_IMG_URL } from "../utils/config";

function ContactInfo() {
  const dispatch = useDispatch();

  // Ensure we load the settings just like in Logo
  useEffect(() => {
    dispatch(fetchSettings());
  }, [dispatch]);

  const { settingsData, loading, error } = useSelector(
    (state) => state.settingsData
  );

  // Prevent rendering before data is ready
  if (loading) {
    return <div className="text-gray-500">Loading contact info...</div>;
  }

  if (error) {
    return <div className="text-red-500">Error loading contact info</div>;
  }

  const contact = settingsData?.settings?.[0];

  // Safety check
  if (!contact) {
    return <div className="text-gray-400">No contact info available.</div>;
  }

const { address, mobile, email } = contact;
const bannerImage = `${BASE_IMG_URL}/contact.jpg`;
  return (
    <div className="w-full mb-6 px-10  bg-white p-8">
       {/* Contact Image */}
        <div className="mt-6">
        <img
            src={bannerImage}
            alt="Contact"
            className="w-full max-w-md rounded-lg shadow-md object-cover"
        />
        </div>
      <ul className="list-none space-y-2">
        {address && (
          <li>
            <strong>📍 Address:</strong> {address}
          </li>
        )}
        {mobile && (
          <li>
            <strong>📞 Phone:</strong>{" "}
            <a href={`tel:${mobile}`} className="text-blue-600 hover:underline">
              {mobile}
            </a>
          </li>
        )}
        {email && (
          <li>
            <strong>✉️ Email:</strong>{" "}
            <a
              href={`mailto:${email}`}
              className="text-blue-600 hover:underline"
            >
              {email}
            </a>
          </li>
        )}
      </ul>
    </div>
  );
}

export default memo(ContactInfo);
