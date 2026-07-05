import React from "react";
import Particles from "react-tsparticles";
import { loadFull } from "tsparticles";

const AnimatedBanner = ({ heading = "Contact Us", subtext = "" }) => {
  const particlesInit = async (main) => {
    await loadFull(main);
  };

  return (
    <div className="relative w-full h-64 overflow-hidden">
      <Particles
        init={particlesInit}
        options={{
          fullScreen: false,
          background: {
            color: {
              value: "#0f172a", // Background color (tailwind slate-900)
            },
          },
          fpsLimit: 60,
          particles: {
            number: {
              value: 40,
              density: {
                enable: true,
                area: 800,
              },
            },
            color: {
              value: ["#38bdf8", "#9333ea"], // Net/Bubble color (sky-400, purple-600)
            },
            shape: {
              type: "circle",
            },
            opacity: {
              value: 0.5,
            },
            size: {
              value: 6,
              random: true,
            },
            move: {
              enable: true,
              speed: 2,
              direction: "none",
              outMode: "bounce",
            },
          },
          interactivity: {
            events: {
              onHover: {
                enable: true,
                mode: "repulse",
              },
              onClick: {
                enable: true,
                mode: "push",
              },
            },
            modes: {
              repulse: {
                distance: 100,
                duration: 0.4,
              },
              push: {
                quantity: 4,
              },
            },
          },
        }}
        className="absolute inset-0"
      />

      <div className="relative z-10 flex flex-col justify-center items-center h-full text-center px-4">
        <h1 className="text-4xl font-bold text-white drop-shadow-lg">{heading}</h1>
        {subtext && (
          <p className="text-white mt-2 text-lg max-w-xl">{subtext}</p>
        )}
      </div>
    </div>
  );
};

export default AnimatedBanner;
