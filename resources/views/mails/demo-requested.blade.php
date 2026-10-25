<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Demo Requested</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; padding: 20px; color: #1f2937;">
    <div style="max-w: 600px; margin: 0 auto; bg-color: #ffffff; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="color: #4f46e5; margin-top: 0;">🎉 New Demo Request Received</h2>
        <p style="font-size: 16px; line-height: 1.5;">A prospect has requested a free demo for the platform. Here are their details:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; font-weight: bold; width: 35%;">Name:</td>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb;">{{ $formData['name'] }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; font-weight: bold;">Work Email:</td>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb;"><a href="mailto:{{ $formData['email'] }}" style="color: #4f46e5;">{{ $formData['email'] }}</a></td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; font-weight: bold;">Company:</td>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb;">{{ $formData['company'] }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; font-weight: bold;">Estimated Users:</td>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb;">{{ $formData['teamSize'] }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; font-weight: bold;">Preferred Date:</td>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb;">{{ $formData['demo_date'] }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; font-weight: bold;">Preferred Time:</td>
                <td style="padding: 10px 0; border-b: 1px solid #e5e7eb; text-transform: capitalize;">{{ $formData['demo_time'] }}</td>
            </tr>
        </table>

        <p style="text-align: center; margin-top: 24px;">
            <a href="{{ route('admin.demos.index') }}" style="display: inline-block; background: #1F6FEB; color: #ffffff; text-decoration: none; font-weight: bold; padding: 10px 18px; border-radius: 8px;">Schedule their demo workspace</a>
        </p>

        <p style="font-size: 14px; color: #6b7280; text-align: center; margin-top: 30px;">
            Reply directly to this email to coordinate the calendar invite link.
        </p>
    </div>
</body>
</html>