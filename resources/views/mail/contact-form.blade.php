<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Message</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 30px;
            background-color: #ffffff;
        }

        .sender-name {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 15px;
            color: #111111;
        }

        .message-content {
            margin-bottom: 40px;
            white-space: pre-wrap;
        }

        .contact-link {
            color: #0ea5e9;
            text-decoration: underline;
            font-size: 13px;
            display: block;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    <div class="sender-name">{{ $formData['name'] }}</div>
    <div class="message-content">{{ $formData['message'] }}</div>
    <div style="margin-top: 40px;">
        <img src="{{ asset('logo.jpg') }}" alt="QuickJHA" style="height: 60px; width: auto; margin-bottom: 10px; display: block;">
        <a href="mailto:{{ $formData['email'] }}" class="contact-link">{{ $formData['email'] }}</a>
        <a href="https://quickjha.zarqsolution.com" class="contact-link" target="_blank">quickjha.zarqsolution.com</a>
    </div>
</body>

</html>