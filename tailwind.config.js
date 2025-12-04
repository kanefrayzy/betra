/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Livewire/**/*.php",
  ],
  safelist: [
    // Только критичные динамические классы
    {
      pattern: /^(bg|text|border)-(primary|success|warning|danger|dark)(-\d+)?$/,
      variants: ['hover', 'focus'],
    },
    // Добавить используемые динамические классы из категорий
    'bg-[#ffb300]',
    'text-[#ffb300]',
    'bg-[#8b5cf6]',
    'text-[#8b5cf6]',
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#00a8ff',
        'primary-dark': '#0090e0',
        'primary-light': '#33b9ff',
        'secondary': '#9333ea',
        'secondary-dark': '#7e22ce',
        'success': '#10b981',
        'warning': '#f59e0b',
        'danger': '#ef4444',
        'dark': {
          DEFAULT: '#0e1116',
          '50': '#f8fafc',
          '100': '#f1f5f9',
          '200': '#e2e8f0',
          '300': '#cbd5e1',
          '400': '#94a3b8',
          '500': '#64748b',
          '600': '#475569',
          '700': '#334155',
          '800': '#1e293b',
          '900': '#0f172a',
          '950': '#0e1116',
        },
        'customDark': '#1a2c38',
        'customHeader': '#1a2c38',
        'customBoldDark': '#0f212e',
        'bgMessage': '#213743',
      },
      fontFamily: {
        'sans': ['Manrope', 'sans-serif'],
        'manrope': ['Manrope', 'sans-serif'],
      },
      boxShadow: {
        'glow': '0 0 15px rgba(0, 168, 255, 0.5)',
        'neon': '0 0 5px rgba(0, 168, 255, 0.5), 0 0 20px rgba(0, 168, 255, 0.3)',
        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
      },
    },
  },
  plugins: [],
}
