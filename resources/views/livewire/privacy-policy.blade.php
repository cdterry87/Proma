<x-card class="prose">
    <h1>Privacy Policy</h1>

    <p><strong>Effective date:</strong> November 5, 2026</p>

    <p>
        This policy explains what {{ config('app.name') }} collects, how it is used, and how it is protected.
        By using {{ config('app.name') }}, you agree to it.
    </p>

    <h2>1. Information we collect</h2>
    <ul>
        <li><strong>Account details:</strong> your name, email address, optional job title, and a hashed copy of your password.</li>
        <li><strong>Content you enter:</strong> the clients, contacts, projects, issues, and tasks you add.</li>
        <li><strong>Technical information:</strong> your IP address and browser details, which are kept with your session and used to protect sign-in.</li>
    </ul>

    <h2>2. How we use your information</h2>
    <ul>
        <li>To provide the application and show you your own records.</li>
        <li>To secure your account, including limiting repeated sign-in attempts.</li>
        <li>To send email, as described below.</li>
    </ul>

    <h2>3. Email</h2>
    <p>
        We send password reset messages when you request them, and notification emails about your projects, issues,
        and tasks. You can turn notification emails off at any time in Settings. Demo accounts never receive email.
    </p>

    <h2>4. Information sharing</h2>
    <p>
        We do not sell or trade your personal information. We share it only with the services needed to run the
        application, such as hosting and email delivery, or when the law requires it.
    </p>

    <h2>5. Cookies and local storage</h2>
    <p>
        We use cookies to keep you signed in and to protect forms. Your browser stores your color theme locally.
        We do not use advertising or analytics cookies, and fonts are served from our own server rather than a third party.
    </p>

    <h2>6. Security</h2>
    <p>We use reasonable measures to protect your information, but no system can guarantee complete security.</p>

    <h2>7. Third-party links</h2>
    <p>{{ config('app.name') }} may link to other websites. We are not responsible for their content or privacy practices.</p>

    <h2>8. Children's privacy</h2>
    <p>{{ config('app.name') }} is not intended for children under 13, and we do not knowingly collect their personal information.</p>

    <h2>9. Changes to this policy</h2>
    <p>We may update this policy and will change the effective date above when we do.</p>

    <h2>10. Contact</h2>
    <p>Questions about this policy can be sent through <a href="{{ config('proma.contact_url') }}" rel="noopener">{{ config('proma.contact_url') }}</a>.</p>
</x-card>
