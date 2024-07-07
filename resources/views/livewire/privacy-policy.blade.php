<div class="flex flex-col gap-6 p-8">
    @if (!auth()->check())
        <div class="flex flex-col items-center justify">
            <div class="logo text-4xl select-none px-2">
                {{ config('app.name') }}
            </div>
            <p>Simplify your project management.</p>
        </div>
    @endif

    <div class="flex items-center justify-center">
        <x-layouts.card
            title="Privacy Policy"
            class="w-full sm:w-144"
        >
            <div class="flex flex-col gap-4">
                <p><strong>Effective Date:</strong> 01/01/2024</p>

                <p>
                    Thank you for visiting Proma. This Privacy Policy outlines how we collect, use, disclose, and
                    safeguard
                    your
                    personal information. By using Proma, you agree to the terms of this Privacy Policy.
                </p>

                <div>
                    <h2 class="text-lg font-bold">1. Information We Collect:</h2>

                    <p>We may collect the following types of information when you use Proma:</p>

                    <ul>
                        <li><strong>Personal Information:</strong> Name, email address, contact details, and any
                            other
                            information
                            you
                            provide voluntarily.</li>
                        <li><strong>Usage Information:</strong> Information about how you use Proma, including IP
                            address,
                            browser
                            type,
                            device information, and pages visited.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg font-bold">2. How We Use Your Information:</h2>

                    <p>We use the collected information for the following purposes:</p>

                    <ul>
                        <li><strong>Provide Services:</strong> To deliver the services you request and to
                            personalize
                            your
                            experience on
                            Proma.</li>
                        <li><strong>Communication:</strong> To respond to your inquiries, provide updates, and send
                            relevant
                            information.</li>
                        <li><strong>Analytics:</strong> To analyze usage patterns, improve our services, and enhance
                            user
                            experience.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg font-bold">3. Information Sharing:</h2>

                    <p>We do not sell, trade, or otherwise transfer your personal information to third parties
                        without
                        your
                        consent,
                        except as described in this Privacy Policy or as required by law.</p>
                </div>

                <div>
                    <h2 class="text-lg font-bold">4. Cookies and Similar Technologies:</h2>

                    <p>Proma may use cookies and similar technologies to enhance user experience, analyze trends,
                        and
                        gather
                        information
                        about users' interactions with the site.</p>
                </div>

                <div>
                    <h2 class="text-lg font-bold">5. Security:</h2>

                    <p>We implement reasonable security measures to protect your personal information from
                        unauthorized
                        access,
                        disclosure, alteration, and destruction.</p>
                </div>

                <div>
                    <h2 class="text-lg font-bold">6. Third-Party Links:</h2>

                    <p>Proma may contain links to third-party websites. We are not responsible for the privacy
                        practices
                        or
                        content
                        of
                        these third-party sites.</p>
                </div>

                <div>
                    <h2 class="text-lg font-bold">7. Children's Privacy:</h2>

                    <p>Proma is not intended for children under the age of 13. We do not knowingly collect personal
                        information
                        from
                        children under 13 years of age.</p>
                </div>

                <div>
                    <h2 class="text-lg font-bold">8. Changes to Privacy Policy:</h2>

                    <p>We reserve the right to update this Privacy Policy. Please review it periodically for any
                        changes.
                    </p>
                </div>

                <div>
                    <h2 class="text-lg font-bold">9. Contact Information:</h2>

                    <p>If you have any questions or concerns about this Privacy Policy, please contact us at <a
                            href="mailto:support@proma.com"
                            class="font-bold underline"
                        >support@proma.com</a>.</p>
                </div>
            </div>
        </x-layouts.card>
    </div>

    <div class="flex items-center justify-center">
        <a
            href="{{ route('home') }}"
            class="flex items-center gap-1 hover:brightness-75"
        >
            <x-icons.arrow-left />
            Back to Home
        </a>
    </div>
</div>
