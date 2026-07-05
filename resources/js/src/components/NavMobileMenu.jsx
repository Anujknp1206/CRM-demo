import React, { memo, useEffect } from 'react';
import { RiCloseLine } from '@remixicon/react';
import { motion, AnimatePresence } from 'framer-motion';
import { MobileNavLinks } from '.';

/**
 * Mobile navigation menu with slide-in animation
 * @param {Array} navLinks - Navigation links data
 * @param {boolean} isOpen - Whether the menu is open
 * @param {Function} onClose - Function to close the menu
 */
const NavMobileMenu = ({ navLinks = [], isOpen, onClose }) => {
  // Prevent body scroll when menu is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }

    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  // Close menu on escape key press
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };

    window.addEventListener('keydown', handleEscape);
    return () => window.removeEventListener('keydown', handleEscape);
  }, [isOpen, onClose]);

  return (
    <AnimatePresence>
      {isOpen && (
        <motion.div
          className='fixed inset-0 z-50 bg-black/60 flex md:hidden'
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          onClick={(e) => {
            // Close menu if clicking outside menu content
            if (e.target === e.currentTarget) {
              onClose();
            }
          }}
        >
          <motion.div
            className='w-2/3 bg-white h-full shadow-lg overflow-y-auto'
            initial={{ x: '-100%' }}
            animate={{ x: 0 }}
            exit={{ x: '-100%' }}
            transition={{ type: 'tween', duration: 0.3 }}
            role="dialog"
            aria-modal="true"
            aria-label="Navigation menu"
          >
            <div className='flex justify-between items-center p-4 bg-blue-600 text-white sticky top-0 z-10'>
              <p className='uppercase text-sm font-bold'>Menu</p>
              <button
                className="p-1 hover:bg-blue-700 rounded-full transition-colors"
                onClick={onClose}
                aria-label="Close menu"
              >
                <RiCloseLine size={22} className='cursor-pointer' />
              </button>
            </div>
            <MobileNavLinks navLinks={navLinks} onClick={onClose} />
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
};

export default memo(NavMobileMenu);