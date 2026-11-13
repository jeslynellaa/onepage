<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:24px;">
    <tr>
        <td style="margin: 0 auto;">
            <table cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;padding:32px;">
                <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif;color:#333333;">
                        <h2 style="margin-top:0;margin-bottom:16px;font-size:20px;font-weight:bold;">
                            {{ $headline }}
                        </h2>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:16px;">
                            Hello {{ $recipient->first_name }},
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:16px;">
                            {{ $body }}
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:20px;">
                            <span style="color:#777777;">{{ $documentType }}:</span><br>
                            <strong>{{ $documentLabel }}</strong>
                        </p>

                        @if ($reason)
                            <p style="font-size:14px;line-height:1.6;margin-bottom:4px;color:#777777;">Reason:</p>
                            <p style="font-size:14px;line-height:1.6;margin-top:0;margin-bottom:20px;padding:12px 16px;background-color:#f9fafb;border-left:3px solid #e5e7eb;white-space:pre-line;">{{ $reason }}</p>
                        @endif

                        <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <a href="{{ $url }}"
                                    style="display:inline-block;background-color:#2563eb;color:#ffffff;
                                            padding:12px 20px;text-decoration:none;
                                            font-size:14px;font-weight:bold;
                                            border-radius:6px;">
                                        {{ $buttonLabel }}
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

                        <p style="font-size:12px;color:#777777;line-height:1.5;margin-bottom:0;">
                            You received this because of your role on this document in OnePage.<br>
                            — OnePage by FCU Team
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
