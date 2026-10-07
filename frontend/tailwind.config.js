/** Configuração do Tailwind CSS. */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#f7f1fa',
          100: '#eee1f3',
          200: '#dec4e8',
          300: '#c99bd8',
          400: '#aa66bd',
          500: '#873e9e',
          600: '#6b2b82',
          700: '#54216e',
          800: '#431958',
          900: '#351344',
        },
        accent: {
          50: '#fff6ed',
          100: '#ffead4',
          500: '#e7611c',
          600: '#cd4c12',
          700: '#aa3812',
        },
      },
    },
  },
  plugins: [],
}
