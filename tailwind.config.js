import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: { sans: ['Figtree', ...defaultTheme.fontFamily.sans] },
      boxShadow: {
        soft: '0 10px 30px -10px rgba(2,6,23,.25)',
        glass: '0 20px 60px -15px rgba(0,0,0,.45)',
      },
      keyframes: {
        'fade-up': { '0%': {opacity:0,transform:'translateY(12px)'}, '100%': {opacity:1,transform:'translateY(0)'} },
        'fade-right': { '0%': {opacity:0,transform:'translateX(-14px)'}, '100%': {opacity:1,transform:'translateX(0)'} },
        'blur-in': { '0%': {filter:'blur(8px)',opacity:0}, '100%': {filter:'blur(0)',opacity:1} },
        'pulse-glow': { '0%,100%':{boxShadow:'0 0 0 0 rgba(20,184,166,.0)'}, '50%':{boxShadow:'0 0 0 8px rgba(20,184,166,.08)'} },
      },
      animation: {
        'fade-up':'fade-up .6s ease-out both',
        'fade-right':'fade-right .7s ease-out both',
        'blur-in':'blur-in .5s ease-out both',
        'pulse-glow':'pulse-glow 2.2s ease-in-out infinite',
      },
    },
  },
  plugins: [forms({ strategy: 'class' })],
}
