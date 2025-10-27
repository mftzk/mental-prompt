/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        colors: {
            'primary-green': '#3F7D58',
            'primary-white': '#EFEFEF',
            'primary-orange': '#EF9651',
            'primary-red-orange': '#EC5228',
        }
    },
  },
  plugins: [],
} 