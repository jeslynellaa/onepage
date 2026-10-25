<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your OnePage demo is ready</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; padding: 20px; color: #1f2937; margin: 0;">
    @php
        $opens = $demo->demo_starts_at;
        $closes = $demo->demo_expires_at;
        $contact = \App\Mail\DemoInvitationMail::CONTACT_EMAIL;
    @endphp
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; line-height: 1.6; margin-top: 0;">Hi {{ $recipientName ?: 'there' }},</p>

        <p style="font-size: 16px; line-height: 1.6;">
            Thank you for booking a demo of OnePage! We've set up a private demo workspace for
            <strong>{{ $demo->name }}</strong> so you can explore the platform at your own pace.
        </p>

        <h3 style="color: #0B1020; margin: 24px 0 8px;">Your demo schedule</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 30%;">Opens:</td>
                <td style="padding: 8px 0;">{{ $opens->format('l, F j, Y') }} at {{ $opens->format('g:i A') }} (Philippine Time)</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Closes:</td>
                <td style="padding: 8px 0;">{{ $closes->format('l, F j, Y') }} at {{ $closes->format('g:i A') }} (Philippine Time)</td>
            </tr>
        </table>

        <p style="text-align: center; margin: 28px 0 12px;">
            <a href="{{ $link }}" style="display: inline-block; background: #1F6FEB; color: #ffffff; text-decoration: none; font-weight: bold; padding: 12px 22px; border-radius: 8px;">Open your demo workspace</a>
        </p>
        <p style="font-size: 13px; line-height: 1.6; color: #6b7280; text-align: center; margin-top: 0;">
            Button not working? Copy this link into your browser:<br>
            <a href="{{ $link }}" style="color: #1F6FEB; word-break: break-all;">{{ $link }}</a>
        </p>

        <p style="font-size: 15px; line-height: 1.6;">
            No username or password is needed — just click the link. If you open it before the start time, you'll see
            when it opens, and the same link will take you in once it does.
        </p>

        <h3 style="color: #0B1020; margin: 24px 0 8px;">What you can try</h3>
        <p style="font-size: 15px; line-height: 1.6; margin-top: 0;">
            The workspace is filled with sample procedures at every stage of the document control workflow. Use the
            <strong>"Viewing as"</strong> menu at the top of the screen to switch between roles — Document Controller,
            Process Owner, Reviewer, Approver and Top Management — and follow a document from draft through review and
            approval to release.
        </p>

        <h3 style="color: #0B1020; margin: 24px 0 8px;">A few things to note</h3>
        <ul style="font-size: 15px; line-height: 1.6; padding-left: 20px; margin-top: 0;">
            <li>The workspace is private to your company. You're welcome to share the link with colleagues who'd like to take a look.</li>
            <li>Feel free to create, edit and approve documents; nothing you do affects any real data.</li>
            <li>When the demo closes, the workspace and everything in it is permanently deleted.</li>
        </ul>

        <h3 style="color: #0B1020; margin: 24px 0 8px;">Ready to proceed, or have questions?</h3>
        <p style="font-size: 15px; line-height: 1.6; margin-top: 0;">
            If you'd like to move forward with OnePage, need more time with the demo, or have any questions at all, just
            reply to this email or write to us at <a href="mailto:{{ $contact }}" style="color: #1F6FEB;">{{ $contact }}</a>.
            We'd be happy to help.
        </p>

        <p style="font-size: 15px; line-height: 1.6; margin-bottom: 0;">
            Best regards,<br>
            <strong>The OnePage Team</strong><br>
            FCU Solutions Inc.<br>
            <a href="mailto:{{ $contact }}" style="color: #1F6FEB;">{{ $contact }}</a>
        </p>
    </div>
</body>
</html>
