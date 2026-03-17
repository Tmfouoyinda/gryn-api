<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Message de contact — GRYN</title>
</head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: auto; padding: 24px;">
    <h2 style="color: #059669;">Nouveau message de contact</h2>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 100px;">Nom :</td>
            <td style="padding: 8px 0;">{{ $data['name'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Email :</td>
            <td style="padding: 8px 0;">
                <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Sujet :</td>
            <td style="padding: 8px 0;">{{ $data['subject'] }}</td>
        </tr>
    </table>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;" />

    <p style="font-weight: bold; margin-bottom: 8px;">Message :</p>
    <p style="white-space: pre-line; background: #f9fafb; padding: 16px; border-radius: 8px;">{{ $data['message'] }}</p>
</body>
</html>
