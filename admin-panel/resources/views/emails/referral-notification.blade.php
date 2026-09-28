<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $heading }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#093162 0%,#297a7b 100%);padding:32px 24px;text-align:center;color:#ffffff;">
                            <div style="font-size:48px;line-height:1;">🎁</div>
                            <h1 style="margin:12px 0 0;font-size:22px;font-weight:700;">{{ $heading }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 16px;">
                            <p style="margin:0 0 12px;font-size:16px;">{{ __('Hi') }} {{ $recipientName }},</p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#444;">
                                {!! nl2br(e($body)) !!}
                            </p>

                            @if($ctaUrl)
                                <div style="text-align:center;margin:28px 0 8px;">
                                    <a href="{{ $ctaUrl }}"
                                       style="display:inline-block;padding:12px 28px;background:linear-gradient(135deg,#093162 0%,#297a7b 100%);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:15px;">
                                        {{ $ctaLabel ?: 'View details' }}
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 28px;">
                            <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
                            <p style="margin:0;font-size:12px;color:#888;text-align:center;">
                                Upskills Academy — {{ now()->format('Y') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
