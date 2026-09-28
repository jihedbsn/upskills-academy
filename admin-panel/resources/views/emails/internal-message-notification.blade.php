@extends('emails.base-template')

@section('content')
<tr>
    <td style="padding: 40px 30px;">
        <h2 style="color: #093162; margin-bottom: 20px;">
            <i class="fas fa-envelope"></i> You Have a New Message
        </h2>

        <div style="background-color: #f8f9fa; border-left: 4px solid #FCD116; padding: 20px; margin-bottom: 20px;">
            <h3 style="color: #333; margin-top: 0;">{{ $internalMessage->subject }}</h3>
            <p style="color: #666; margin-bottom: 0;">
                {{ $preview }}...
            </p>
        </div>

        <p style="color: #555; line-height: 1.6;">
            The Upskills Academy administration has sent you a new message. Please log in to your student dashboard to read the full message.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
            <tr>
                <td align="center">
                    <a href="{{ $inboxUrl }}"
                       style="display: inline-block; padding: 15px 40px; background-color: #093162; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                        Read Message
                    </a>
                </td>
            </tr>
        </table>

        <div style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 15px; margin-top: 20px;">
            <p style="color: #856404; margin: 0; font-size: 14px;">
                <strong>Note:</strong> This is an automated notification. Please do not reply to this email.
            </p>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 30px; background-color: #f8f9fa; border-top: 1px solid #dee2e6;">
        <p style="color: #6c757d; font-size: 12px; margin: 0; text-align: center;">
            Sent on {{ $internalMessage->created_at->format('F d, Y \a\t H:i') }}
        </p>
    </td>
</tr>
@endsection
