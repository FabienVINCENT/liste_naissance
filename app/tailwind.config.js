module.exports = {
  content: [
    'templates/**/*.html.twig',
    'assets/js/**/*.js',
  ],
  theme: {
    extend: {
      borderRadius: {
        'maxi': '80%',
      },
      colors: {
        'bluegray': '#5F779C',
        'bluegraydark': '#597092',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
