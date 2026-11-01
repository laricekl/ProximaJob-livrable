<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre candidature - ProximaJob</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .button {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            margin: 25px 0;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
        }
        .offer-box {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #059669;
            margin: 20px 0;
        }
        .offer-box h3 {
            margin: 0 0 8px 0;
            color: #059669;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .offer-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        .company-name {
            color: #4a5568;
            margin: 4px 0 0 0;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2d3748;
        }
        .message {
            margin-bottom: 20px;
            color: #4a5568;
        }
        .footer {
            text-align: center;
            padding: 25px;
            font-size: 14px;
            color: #666;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mise à jour de votre candidature</h1>
        </div>

        <div class="content">
            <div class="greeting">Bonjour {{ $candidateName }},</div>

            <div class="message">
                <p>L’entreprise a fait avancer votre dossier : votre candidature a été <strong>{{ $statusMessage }}</strong>.</p>
            </div>

            <div class="offer-box">
                <h3>Offre concernée</h3>
                <p class="offer-title">{{ $offerTitle }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>

            <div class="message">
                <p>Connectez-vous à votre espace pour voir le détail de votre candidature et les prochaines étapes.</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $detailUrl }}" class="button">Voir ma candidature</a>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                Cordialement,<br>
                <strong style="color: #059669;">L'équipe ProximaJob</strong>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 5px 0;"><strong>ProximaJob</strong></p>
            <p style="margin: 0 0 15px 0; color: #059669; font-weight: 500;">La plateforme de recrutement de proximité</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} ProximaJob. Tous droits réservés.</p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #9ca3af;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
