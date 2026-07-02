/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Livewire/**/*.php",
    "./vendor/livewire/flux-pro/stubs/**/*.blade.php",
    "./vendor/livewire/flux/stubs/**/*.blade.php",
  ],


  theme: {
    extend: {},
  },
  plugins: [],
}
