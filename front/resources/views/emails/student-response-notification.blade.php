<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .response-box {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .student-info {
            background-color: #e9ecef;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📨 New Student Response</h2>
    </div>

    <div class="content">
        <p>Hello {{ $adminName }},</p>

        <p>A student has responded to your message: <strong>"{{ $messageSubject }}"</strong></p>

        <div class="student-info">
            <strong>Student Information:</strong><br>
            <strong>Name:</strong> {{ $studentName }}<br>
            <strong>Email:</strong> {{ $studentEmail }}
        </div>

        <div class="response-box">
            <h3>Student's Response:</h3>
            <p style="white-space: pre-wrap;">{{ $responseBody }}</p>
        </div>

        <p style="text-align: center;">
            <a href="{{ env('ADMIN_URL') }}/internal-messages/{{ $messageId }}" class="button">
                View Full Message & Response
            </a>
        </p>

        <p style="color: #6c757d; font-size: 14px;">
            <strong>Note:</strong> This is an automated notification from the internal messaging system.
            You can view and manage all student responses in the admin dashboard.
        </p>
    </div>

    <div class="footer">
        <p>This email was sent automatically by the Upskills Academy Internal Messaging System.</p>
        <p>&copy; {{ date('Y') }} Upskills Academy. All rights reserved.</p>
    </div>
</body>
</html>
