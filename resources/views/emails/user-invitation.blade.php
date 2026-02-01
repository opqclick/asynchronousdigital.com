<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Asynchronous Digital</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body h2 {
            color: #667eea;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .email-body p {
            margin: 15px 0;
            font-size: 16px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .credentials-box .credential-item {
            margin: 10px 0;
        }
        .credentials-box .credential-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .credentials-box .credential-value {
            color: #333;
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        .cta-button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 25px 0;
            text-align: center;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .security-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .security-note p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .support-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .support-info p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Welcome to Asynchronous Digital!</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello {{ $userName }},</h2>
            
            <p>We're excited to have you join our team at Asynchronous Digital! Your account has been successfully created, and you can now access our platform.</p>
            
            <p>Below are your login credentials. Please keep them secure and change your password after your first login.</p>
            
            <div class="credentials-box">
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $userEmail }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="cta-button">Login to Your Account</a>
            </div>
            
            <div class="security-note">
                <p><strong>⚠️ Security Note:</strong> For your security, we recommend changing your password immediately after your first login. You can do this from your profile settings.</p>
            </div>
            
            <div class="support-info">
                <p><strong>Need Help?</strong></p>
                <p>If you have any questions or need assistance, please don't hesitate to reach out to our support team at <a href="mailto:asynchronousd@gmail.com" style="color: #667eea;">asynchronousd@gmail.com</a></p>
            </div>
        </div>
        
        <div class="email-footer">
            <p><strong>Asynchronous Digital</strong></p>
            <p>Building Digital Solutions That Matter</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
