/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            spacing: {
                120: '30rem',
            },
            colors: {
                primary: '#d4af37',
                secondary: '#6f777d',
            },
            padding: {
                '2.5': '0.609rem'
            }
        },
    },
    plugins: [],
}
