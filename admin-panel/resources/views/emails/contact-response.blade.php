<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response to Your Message</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #6440FB;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #6440FB;
            margin: 0;
            font-size: 24px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .original-message {
            background-color: #f8f9fa;
            border-left: 4px solid #ddd;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            color: #666;
        }
        .original-message h4 {
            margin: 0 0 10px 0;
            color: #888;
            font-size: 14px;
        }
        .response-box {
            background-color: #f0f0ff;
            border-left: 4px solid #6440FB;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .response-box h3 {
            margin: 0 0 10px 0;
            color: #6440FB;
            font-size: 16px;
        }
        .response-content {
            white-space: pre-wrap;
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 13px;
        }
        .btn {
            display: inline-block;
            background-color: #6440FB;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Upskills Academy</h1>
        </div>

        <p class="greeting">Dear {{ $contactMessage->name }},</p>

        <p>Thank you for contacting us. Here is our response to your message:</p>

        @if($contactMessage->subject)
        <p><strong>Subject:</strong> {{ $contactMessage->subject }}</p>
        @endif

        <div class="original-message">
            <h4>Your original message:</h4>
            <p>{{ $contactMessage->message }}</p>
        </div>

        <div class="response-box">
            <h3>Our Response:</h3>
            <div class="response-content">{!! nl2br(e($response)) !!}</div>
        </div>

        <p>If you have any further questions, please don't hesitate to contact us again.</p>

        <div style="text-align: center;">
            <a href="{{ \App\Support\StudentFrontendUrl::localized('en', '') }}" class="btn">
                Visit Our Website
            </a>
        </div>

        <div class="footer">
            <p>Best regards,<br>Upskills Academy Team</p>
            <p>© {{ date('Y') }} Upskills Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
