<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body>
    @if($passwordChanged)
        <h1>Password Changed</h1>
        <p>Hello {{ $user->name }},</p>
        <p>Your password has been successfully changed.</p>
        <p>If you did not make this change, please contact support immediately.</p>
    @else
        <h1>Hello {{ $user->name }},</h1>
        <p>Your account information has been updated successfully. Here are the new details:</p>
        <ul>
            <li><strong>Name:</strong> {{ $user->name }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
        </ul>
        <p>If you did not request this change, please contact support immediately.</p>
    @endif
    <p>Thank you!</p>
</body>
</html>
