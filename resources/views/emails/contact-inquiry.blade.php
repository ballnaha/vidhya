<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New project inquiry</title>
</head>
<body style="margin:0;background:#f4f4f5;color:#18181b;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border:1px solid #e4e4e7;padding:32px;">
            <p style="margin:0 0 8px;color:#366bc3;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">Vidhya Studio</p>
            <h1 style="margin:0 0 28px;font-size:26px;line-height:1.2;">New project inquiry</h1>

            <table style="width:100%;border-collapse:collapse;font-size:14px;line-height:1.6;">
                <tr><td style="width:120px;padding:8px 0;color:#71717a;">Name</td><td style="padding:8px 0;font-weight:600;">{{ $name }}</td></tr>
                <tr><td style="padding:8px 0;color:#71717a;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $email }}">{{ $email }}</a></td></tr>
                <tr><td style="padding:8px 0;color:#71717a;">Company</td><td style="padding:8px 0;">{{ filled($company ?? null) ? $company : '—' }}</td></tr>
                <tr><td style="padding:8px 0;color:#71717a;">Service</td><td style="padding:8px 0;">{{ filled($service ?? null) ? $service : '—' }}</td></tr>
            </table>

            <div style="margin-top:24px;border-top:1px solid #e4e4e7;padding-top:24px;">
                <p style="margin:0 0 10px;color:#71717a;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Project details</p>
                <div style="font-size:15px;line-height:1.75;white-space:pre-wrap;">{{ $message }}</div>
            </div>
        </div>
    </div>
</body>
</html>
