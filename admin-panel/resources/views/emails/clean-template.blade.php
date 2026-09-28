@extends('emails.base-template')

@section('title', $subject)

@section('email-title', $subject)

@section('content')
    <div class="content-text">
        {!! nl2br(e($content)) !!}
    </div>
    @if(!empty($trackingPixelUrl ?? null))
        <img src="{{ $trackingPixelUrl }}" width="1" height="1" alt="" style="display:block;width:1px;height:1px;border:0;margin:0;padding:0;overflow:hidden;">
    @endif
    @if(!empty($trackingConfirmUrl ?? null))
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:32px auto 12px;">
            <tr>
                <td align="center">
                    <a href="{{ $trackingConfirmUrl }}"
                       style="display:inline-block;background-color:#2563eb;color:#ffffff !important;font-weight:600;font-size:16px;line-height:1.2;padding:14px 40px;text-decoration:none;border-radius:6px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                        See more
                    </a>
                </td>
            </tr>
        </table>
        <p style="margin:0 24px 16px;font-size:13px;line-height:1.5;color:#64748b;text-align:center;">
            Opens the Upskills Academy website and <strong>confirms you received this email</strong> (subscriber — no attachment required).
        </p>
        <p style="margin:0 16px;font-size:12px;line-height:1.5;color:#94a3b8;text-align:center;">
            If your email client blocks images, this button still records the same delivery confirmation.
        </p>
    @endif
@endsection
