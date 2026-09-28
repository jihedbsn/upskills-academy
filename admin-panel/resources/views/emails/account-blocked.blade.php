@extends('emails.base-template')

@section('content')
<!-- Error Card -->
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td style="padding: 30px; background-color: #ffffff; border-radius: 8px;">
            <!-- Error Icon -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center" style="padding-bottom: 20px;">
                        <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <span style="color: #ffffff; font-size: 32px; font-weight: bold;">!</span>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Title -->
            <h2 style="margin: 0 0 16px 0; color: #1f2937; font-size: 24px; font-weight: 600; text-align: center;">
                Account Blocked
            </h2>

            <!-- Message -->
            <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 16px; line-height: 1.6; text-align: center;">
                Dear {{ $studentName }},
            </p>

            <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 16px; line-height: 1.6;">
                Your account has been blocked and you can no longer access the platform. This action was taken on <strong>{{ $blockedAt->format('F j, Y \a\t g:i A') }}</strong>.
            </p>

            @if($blockReason)
            <!-- Block Reason Box -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
                <tr>
                    <td style="padding: 16px; background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 4px;">
                        <p style="margin: 0 0 8px 0; color: #991b1b; font-size: 14px; font-weight: 600;">
                            Reason for blocking:
                        </p>
                        <p style="margin: 0; color: #7f1d1d; font-size: 14px; line-height: 1.5;">
                            {{ $blockReason }}
                        </p>
                    </td>
                </tr>
            </table>
            @endif

            <!-- What This Means -->
            <div style="margin-bottom: 24px; padding: 16px; background-color: #f9fafb; border-radius: 6px;">
                <h3 style="margin: 0 0 12px 0; color: #374151; font-size: 16px; font-weight: 600;">
                    What This Means:
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #6b7280; font-size: 14px; line-height: 1.8;">
                    <li>You cannot log in to your account</li>
                    <li>Access to all courses and materials is suspended</li>
                    <li>Any active sessions have been terminated</li>
                </ul>
            </div>

            <!-- Next Steps -->
            <div style="margin-bottom: 24px;">
                <h3 style="margin: 0 0 12px 0; color: #374151; font-size: 16px; font-weight: 600;">
                    Next Steps:
                </h3>
                <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                    If you believe this action was taken in error or would like to discuss this matter, please contact our support team:
                </p>
            </div>

            <!-- Contact Support Button -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center" style="padding: 24px 0;">
                        <a href="mailto:support@up-skills.academy" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 600; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);">
                            Contact Support
                        </a>
                    </td>
                </tr>
            </table>

            <!-- Support Information -->
            <div style="padding: 16px; background-color: #f0f9ff; border-radius: 6px; text-align: center;">
                <p style="margin: 0 0 8px 0; color: #1e40af; font-size: 14px;">
                    <strong>Support Email:</strong> support@up-skills.academy
                </p>
                <p style="margin: 0; color: #1e40af; font-size: 14px;">
                    <strong>Student ID:</strong> {{ $student->id }}
                </p>
            </div>
        </td>
    </tr>
</table>
@endsection
