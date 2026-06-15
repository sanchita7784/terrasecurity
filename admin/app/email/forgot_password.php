<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #2d3748;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
            color: #4a5568;
        }
        .content h2 {
            color: #2d3748;
            font-size: 20px;
            margin-top: 0;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            background-color: #4299e1;
            color: #ffffff !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
        }
        .expiry-note {
            font-size: 13px;
            color: #718096;
            margin-top: 20px;
            border-top: 1px solid #edf2f7;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mastropay Payment Gateway</h1>
        </div>
        <div class="content">
            <h2>Password Reset Request</h2>
            <p>Hello,</p>
            <p>We received a request to reset the password for your account. No changes have been made yet.</p>
            <p>You can reset your password by clicking the button below:</p>
            
            <div class="button-container">
                <a href="<?= $link ?>" class="button">Reset Password</a>
            </div>

            <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
            
            <div class="expiry-note">
                <p>This link will expire in 60 minutes for security reasons.</p>
            </div>
        </div>
        <div class="footer">
            &copy; <?= date("Y")  ?> Mastropay Payment Gateway. All rights reserved.<br>
            Global Business Park (3rd Floor) Office No.312 ,Jalandhar Bypass,National Highway 1, Sarop Nagar, Ludhiana, Punjab 141008, India
        </div>
    </div>
</body>
</html>