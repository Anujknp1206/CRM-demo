import { useRef, useEffect, memo } from "react";
import { LeftPanel, RightPanel, SearchBox } from ".";

function ParentBlog() {
  const searchRef = useRef();

  // Send position info to the window so Navbar can listen
  useEffect(() => {
    const handleScroll = () => {
      const rect = searchRef.current?.getBoundingClientRect();
      window.dispatchEvent(
        new CustomEvent("parentSearchPosition", {
          detail: rect?.bottom < 60, // Adjust based on your Navbar height
        })
      );
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <div>
      <div className="mt-10 px-5" ref={searchRef}>
        <SearchBox />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-12 gap-8 p-5 min-h-48 mt-10">
        <LeftPanel />
        <RightPanel />
      </div>
    </div>
  );
}

export default memo(ParentBlog);
