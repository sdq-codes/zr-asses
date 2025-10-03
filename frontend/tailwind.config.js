module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        customPrimaryBlue: "#001414",
      },
      boxShadow: {
        subtle: '10px 30px 20px 10px #1C1B2214',
      }
    },
  },
  plugins: [],
}
