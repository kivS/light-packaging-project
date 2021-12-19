const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    './front-end-pages/*.php'
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
  
  ],
}
