/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#0F0F0F",
        card: "#1A1A1A",
        accent: "#F7941D",
        success: "#2ECC71",
        danger: "#E74C3C",
        textMain: "#EEEEEE",
        textMuted: "#888888",
      }
    },
  },
  plugins: [],
}