<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; padding: 24px 12px; background: #f4f4f5; color: #18181b; font-family: Inter, -apple-system, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 520px;">
                    <tr>
                        <td style="padding: 0 4px 16px; font-size: 16px; font-weight: 600;">{{ config('app.name') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 24px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 8px;">
                            <h1 style="margin: 0 0 8px; font-size: 18px; font-weight: 600;">{{ $title }}</h1>
                            <p style="margin: 0 0 20px; color: #3f3f46;">{{ $body }}</p>
                            <a href="{{ $url }}" style="display: inline-block; padding: 8px 14px; background: #18181b; color: #ffffff; font-weight: 500; text-decoration: none; border-radius: 6px;">View details</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 4px 0; color: #52525b; font-size: 13px;">
                            You can turn off these emails in your <a href="{{ $settingsUrl }}" style="color: #4338ca;">settings</a>.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
