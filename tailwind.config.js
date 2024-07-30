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
                gold: {
                    500: '#d4af37',
                }
            }
        },
    },
    plugins: [],
}
