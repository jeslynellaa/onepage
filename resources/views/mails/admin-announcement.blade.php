<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F7FA;">
    @php
        // Embed the logo so it shows even when the mail client blocks remote images.
        $logo = isset($message) ? $message->embed(public_path('onepage-name.png')) : asset('onepage-name.png');
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F5F7FA;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;">
                    {{-- Logo --}}
                    <tr>
                        <td style="padding:0 4px 20px;">
                            <img src="{{ $logo }}" alt="OnePage" height="26" style="display:block;height:26px;width:auto;border:0;">
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background-color:#FFFFFF;border-radius:16px;border-top:4px solid #1F6FEB;padding:36px 36px 32px;font-family:Arial,Helvetica,sans-serif;color:#0B1020;">
                            <h1 style="margin:0 0 20px;font-size:22px;line-height:1.3;font-weight:bold;color:#0B1020;">
                                {{ $emailSubject }}
                            </h1>

                            <p style="margin:0 0 16px;font-size:15px;line-height:1.65;color:#0B1020;">
                                Hi {{ $recipient->first_name ?: 'there' }},
                            </p>

                            @foreach ($paragraphs as $paragraph)
                                <p style="margin:0 0 16px;font-size:15px;line-height:1.65;color:#3A4256;">
                                    {!! nl2br(e($paragraph)) !!}
                                </p>
                            @endforeach

                            @if ($buttonLabel && $buttonUrl)
                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 8px;">
                                    <tr>
                                        <td style="border-radius:999px;background-color:#1F6FEB;">
                                            <a href="{{ $buttonUrl }}" target="_blank"
                                               style="display:inline-block;padding:13px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:#FFFFFF;text-decoration:none;border-radius:999px;">
                                                {{ $buttonLabel }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:28px 0 0;font-size:15px;line-height:1.65;color:#3A4256;">
                                Best regards,<br>
                                <strong style="color:#0B1020;">The OnePage Team</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 8px 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#8B93A7;text-align:center;">
                            You're receiving this because you have a OnePage account.<br>
                            Questions? Just reply to this email.<br>
                            OnePage by FCU
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
