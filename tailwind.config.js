/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php", 
    "./resources/**/*.vue", 
    "./resources/**/*.js", 
    "./vendor/filament/**/*.blade.php", 
    "./resources/**/*.blade.php", 
    "./resources/**/*.js",

    './vendor/namu/wirechat/resources/views/**/*.blade.php',
    './vendor/namu/wirechat/src/Livewire/**/*.php'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
