{{-- Email clients ignore stylesheets, so styles are inline and the layout is a table. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Reset your {{ config('app.name') }} password</title>
</head>
<body style="margin:0; padding:0; background:#f6f7f9; color:#1f2229; font-family:-apple-system, 'Segoe UI', Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7f9;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;">
                    <tr>
                        <td style="padding:0 0 24px; font-size:18px; font-weight:600; color:#1f2229;">
                            <span style="color:#3f51c9;">&#9646;</span> {{ config('app.name') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px; background:#ffffff; border:1px solid #e6e8ec; border-radius:14px;">
                            <h1 style="margin:0 0 16px; font-size:22px; line-height:1.3; color:#1f2229;">Reset your password</h1>
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">Hi {{ $name }},</p>
                            <p style="margin:0 0 24px; font-size:15px; line-height:1.6;">We received a request to reset the password for your {{ config('app.name') }} account. Choose a new one with the button below.</p>
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:10px; background:#3f51c9;">
                                        <a href="{{ $url }}" style="display:inline-block; padding:12px 24px; color:#ffffff; font-size:15px; font-weight:600; text-decoration:none;">Reset password</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:24px 0 0; font-size:13px; line-height:1.6; color:#5d6270;">This link expires in {{ $expires }} minutes. If you didn't ask to reset your password, you can ignore this email.</p>
                            <p style="margin:16px 0 0; font-size:13px; line-height:1.6; color:#5d6270;">If the button doesn't work, paste this link into your browser:<br><a href="{{ $url }}" style="color:#3f51c9; word-break:break-all;">{{ $url }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 0 0; font-size:12px; line-height:1.6; color:#5d6270; text-align:center;">
                            {{ config('app.name') }} is a portfolio application that is not intended for public use.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
