<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
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
            border-bottom: 2px solid #093162;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #093162;
            margin: 0;
            font-size: 24px;
        }
        .badge {
            display: inline-block;
            background-color: #093162;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            width: 100px;
            flex-shrink: 0;
        }
        .info-value {
            color: #333;
        }
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #093162;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .message-box h3 {
            margin: 0 0 10px 0;
            color: #093162;
            font-size: 16px;
        }
        .message-content {
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
            background-color: #093162;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #5030e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 New Contact Message</h1>
            <span class="badge">Upskills Academy</span>
        </div>

        <p>You have received a new contact message from the website.</p>

        <div class="info-row">
            <span class="info-label">From:</span>
            <span class="info-value">{{ $contactMessage->name }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">
                <a href="mailto:{{ $contactMessage->email }}" style="color: #093162;">{{ $contactMessage->email }}</a>
            </span>
        </div>

        @if($contactMessage->subject)
        <div class="info-row">
            <span class="info-label">Subject:</span>
            <span class="info-value">{{ $contactMessage->subject }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ $contactMessage->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>

        <div class="message-box">
            <h3>Message:</h3>
            <div class="message-content">{{ $contactMessage->message }}</div>
        </div>

        <div style="text-align: center;">
            <a href="{{ env('ADMIN_URL', 'https://admin.upskillsacademy.com') }}/contact-messages/{{ $contactMessage->id }}" class="btn">
                View in Admin Panel
            </a>
        </div>

        <div class="footer">
            <p>This is an automated notification from Upskills Academy.</p>
            <p>© {{ date('Y') }} Upskills Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
