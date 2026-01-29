<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:24px;">
    <tr>
        <td style="margin: 0 auto;">
            <table cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;padding:32px;">
                <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif;color:#333333;">

```
                        <h2 style="margin-top:0;margin-bottom:16px;font-size:20px;font-weight:bold;">
                            You’re Invited to Join {{ $invitation->company->name }} on OnePage
                        </h2>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:16px;">
                            Hello,
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:20px;">
                            Your company, <strong>{{ $invitation->company->name }}</strong>, has invited you to create an account on OnePage, a management tool that helps your team streamline document related processes.

                            An account has been reserved for you, giving you access to tools and information relevant to your role within the company. 
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:20px;">
                            To get started, please follow these simple steps:
                        </p>

                        <ol style="font-size:14px;line-height:1.6;margin-bottom:20px;padding-left:20px;">
                            <li>Click the <strong>“Accept Invitation”</strong> button below.</li>
                            <li>You will be redirected to the registration page.</li>
                            <li>Complete your account setup by filling in the required details and setting a password.</li>
                            <li>Once registered, you can log in and start accessing the tools and information provided for your role.</li>
                        </ol>

                        <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <a href=""
                                    style="display:inline-block;background-color:#2563eb;color:#ffffff;
                                            padding:12px 20px;text-decoration:none;
                                            font-size:14px;font-weight:bold;
                                            border-radius:6px;">
                                        Accept Invitation
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:13px;line-height:1.6;color:#555555;margin-bottom:20px;">
                            This invitation was sent on behalf of <strong>{{ $invitation->company->name }}</strong>.
                            If you weren’t expecting this invitation, you may safely ignore this email.
                        </p>

                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

                        <p style="font-size:12px;color:#777777;line-height:1.5;">
                            If you have questions or need assistance, feel free to reply to this email.
                        </p>

                        <p style="font-size:12px;color:#777777;line-height:1.5;margin-bottom:0;">
                            — The OnePage by FCU Team
                        </p>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>