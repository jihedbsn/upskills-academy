<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 15px 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Upskills Academy</h1>
            <p>Your partner for English excellence</p>
        </div>
        <div class="content">
            <p>Dear {{ $student_name ?? $student_first_name ?? 'Student' }},</p>
            <p>This is a notification regarding your progress in {{ $course_name ?? 'your course' }}.</p>
            
            @if(isset($admin_notes) && $admin_notes)
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0;">
                    <strong>Note:</strong> {{ $admin_notes }}
                </div>
            @endif
            
            <p>If you have any questions, please don't hesitate to contact our support team.</p>
            <p>Best regards,<br>The Upskills Academy Team</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Upskills Academy. All rights reserved.<br>
            📧 no_reply@up-skills.academy | 🌐 www.up-skills.academy
        </div>
    </div>
</body>
</html>
