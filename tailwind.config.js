import forms from '@tailwindcss/forms';

export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/**/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php'
  ],
  theme: {
    extend: {
      colors: {
        primary: "#1f4b8e",
        "primary-dark": "#102a52",
        secondary: "#182430",
        "secondary-dark": "#060C11",
      }
    },
  },
  plugins: [forms],
}
