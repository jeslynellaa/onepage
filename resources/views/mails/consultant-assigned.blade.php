<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:24px;">
    <tr>
        <td style="margin: 0 auto;">
            <table cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;padding:32px;">
                <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif;color:#333333;">
                        <h2 style="margin-top:0;margin-bottom:16px;font-size:20px;font-weight:bold;">
                            You Have Been Assigned to {{ $client->name }}
                        </h2>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:16px;">
                            Hello {{ $consultant->first_name }},
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:20px;">
                            You have been assigned as a consultant for <strong>{{ $client->name }}</strong> and can now access their space on OnePage.
                        </p>

                        <p style="font-size:14px;line-height:1.6;margin-bottom:20px;">
                            To access their space:
                        </p>

                        <ol style="font-size:14px;line-height:1.6;margin-bottom:20px;padding-left:20px;">
                            <li>Click the <strong>“Go to My Clients”</strong> button below and log in with your usual OnePage account.</li>
                            <li>On the <strong>My Clients</strong> page, which you can also open any time from <strong>Clients</strong> in the sidebar, find <strong>{{ $client->name }}</strong>.</li>
                            <li>Click <strong>Enter</strong>. A banner at the top of the page will show <strong>“Working in: {{ $client->name }}”</strong>.</li>
                            <li>When you are finished, click <strong>Exit</strong> on that banner to return to your own company’s space.</li>
                        </ol>

                        <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <a href="{{ route('consultant.clients') }}"
                                    style="display:inline-block;background-color:#2563eb;color:#ffffff;
                                            padding:12px 20px;text-decoration:none;
                                            font-size:14px;font-weight:bold;
                                            border-radius:6px;">
                                        Go to My Clients
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

                        <p style="font-size:12px;color:#777777;line-height:1.5;margin-bottom:0;">
                            — OnePage by FCU Team
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
