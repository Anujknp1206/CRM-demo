// development only console logs
export const devlog = (...args) => {
  if (process.env.NODE_ENV === "development") {
    console.log("[devlogs]", ...args);
  }
}

// Prevent text copy
export const preventCopyText = (e) => {
  // e.preventDefault();
}