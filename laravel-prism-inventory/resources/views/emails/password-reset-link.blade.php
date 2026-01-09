<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Password Reset</title>
</head>
<body>
  <p>Hello {{ $user->username }},</p>

  <p>You (or someone else) requested a password reset for your PRISM account.</p>

  <p>
    Click the link below to reset your password. This link will expire in 60 minutes.
  </p>

  <p>
    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
  </p>

  <p>If you did not request this, you can ignore this email.</p>

  <p>Regards,<br>PRISM</p>
</body>
</html>