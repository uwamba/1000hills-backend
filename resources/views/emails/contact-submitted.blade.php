<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>New Contact Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7fafc; margin:0; padding:20px; color: #333;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding: 30px;">
    <tr>
      <td style="border-bottom: 3px solid #2b6cb0; padding-bottom: 15px; text-align:center;">
        <h2 style="color: #2b6cb0; font-weight: 700; margin: 0;">New Contact Form Submission</h2>
      </td>
    </tr>
    <tr>
      <td style="padding-top: 20px; font-size: 16px; line-height: 1.5;">
        <p><strong style="color: #2b6cb0;">Name:</strong> {{ $contact->name }}</p>
        <p><strong style="color: #2b6cb0;">Phone:</strong> {{ $contact->phone }}</p>
        <p><strong style="color: #2b6cb0;">Email:</strong> {{ $contact->email }}</p>
        <p><strong style="color: #2b6cb0;">Address:</strong> {{ $contact->address }}</p>
        <p><strong style="color: #2b6cb0;">Issue:</strong> {{ $contact->issue }}</p>
        <p><strong style="color: #2b6cb0;">Category:</strong> {{ $contact->category }}</p>
        <p><strong style="color: #2b6cb0;">Description:</strong></p>
        <p style="background-color: #edf2f7; padding: 15px; border-radius: 5px; white-space: pre-wrap;">{{ $contact->description }}</p>
      </td>
    </tr>
  </table>
</body>
</html>
