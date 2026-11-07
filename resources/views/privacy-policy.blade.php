<x-layouts::guest title="Privacy Policy" wide>
    <x-ui.card class="legal">
        <header class="legal__header">
            <h1 class="legal__title">Privacy Policy</h1>
            <p class="legal__meta">Effective November 7, 2026</p>
        </header>

        <p class="notice notice--info">
            {{ config('app.name') }} is a portfolio application that is not intended for public use.
            Please don't enter real or sensitive financial information.
        </p>

        <p>This policy explains what {{ config('app.name') }} stores, why, and who else sees it. By using {{ config('app.name') }}, you agree to it.</p>

        <section>
            <h2>1. Information we collect</h2>
            <ul>
                <li><strong>Account details:</strong> your name, email address, password (stored only as a secure hash), income, pay frequency, and timezone.</li>
                <li><strong>Bills:</strong> the names, categories, descriptions, amounts, due days, and months you enter.</li>
                <li><strong>Technical information:</strong> your IP address and browser details, used to limit repeated sign-in attempts, check registrations for bots, and keep server logs.</li>
            </ul>
        </section>

        <section>
            <h2>2. How we use your information</h2>
            <p>Only to run {{ config('app.name') }}: to show your bills and totals, to send password reset emails you ask for, and to protect the application from abuse. There are no analytics, advertising, or marketing emails.</p>
        </section>

        <section>
            <h2>3. Information sharing</h2>
            <p>Your information is never sold or traded. It is shared only with the services that run {{ config('app.name') }}, or when the law requires it:</p>
            <ul>
                <li><strong>Cloudflare Turnstile</strong> checks the registration form for bots, and receives your IP address and browser signals to do so.</li>
                <li><strong>Resend</strong> delivers password reset emails, and receives your email address to do so.</li>
                <li>The <strong>hosting provider</strong> stores the application and its database.</li>
            </ul>
        </section>

        <section>
            <h2>4. Cookies and local storage</h2>
            <p>{{ config('app.name') }} uses a session cookie and a security token cookie to keep you signed in and protect forms, and a "remember me" cookie only if you choose it. Your light or dark theme choice is kept in your browser's local storage. Cloudflare Turnstile may set its own cookies on the registration page. There are no tracking cookies.</p>
        </section>

        <section>
            <h2>5. Security and retention</h2>
            <p>Passwords are hashed, connections are encrypted, and sign-in attempts are rate limited. No method of storage or transfer is completely secure, however. Your information is kept until you delete your account from <strong>My profile</strong>, which removes your account and all of your bills immediately. The shared demo account is reset every day.</p>
        </section>

        <section>
            <h2>6. Third-party links</h2>
            <p>{{ config('app.name') }} links to a few other websites. We are not responsible for their content or privacy practices.</p>
        </section>

        <section>
            <h2>7. Children's privacy</h2>
            <p>{{ config('app.name') }} is not intended for children under 13, and we do not knowingly collect information from them.</p>
        </section>

        <section>
            <h2>8. Changes to this policy</h2>
            <p>This policy may change. The effective date above shows when it last did.</p>
        </section>

        <section>
            <h2>9. About this application</h2>
            <p>{{ config('app.name') }} is a portfolio application by <a href="https://chaseterry.com" rel="noopener" target="_blank">Chase Terry</a> and is not intended for public use. There is no support mailbox.</p>
        </section>

        <p><a href="{{ url('/') }}">Back to {{ config('app.name') }}</a></p>
    </x-ui.card>
</x-layouts::guest>
