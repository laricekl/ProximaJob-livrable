<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre email - ProximaJob</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            padding: 40px 30px; 
            text-align: center; 
            color: white; 
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content { 
            padding: 40px 30px; 
        }
        .button { 
            display: inline-block; 
            padding: 14px 35px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            margin: 25px 0; 
            font-weight: 600;
            font-size: 16px;
            text-align: center;
        }
        .footer { 
            text-align: center; 
            padding: 25px; 
            font-size: 14px; 
            color: #666; 
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .verification-link {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 14px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2d3748;
        }
        .instructions {
            margin-bottom: 25px;
            color: #4a5568;
        }
        .expiry-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vérification de votre email</h1>
        </div>
        <div class="content">
            <div class="greeting">Bonjour {{ $user->prenom }} {{ $user->name }},</div>
            
            <div class="instructions">
                <p>Merci de vous être inscrit sur <strong>ProximaJob</strong>. Pour activer votre compte et commencer à utiliser nos services, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous :</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Vérifier mon email</a>
            </div>

            <div class="expiry-notice">
                 <strong>Important :</strong> Ce lien expirera dans 24 heures.
            </div>

            <p>Si le bouton ne fonctionne pas, vous pouvez copier-coller le lien suivant dans votre navigateur :</p>
            
            <div class="verification-link">
                {{ $verificationUrl }}
            </div>

            <p style="color: #718096; font-size: 14px; margin-top: 25px;">
                Si vous n'avez pas créé de compte sur ProximaJob, veuillez ignorer cet email.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} ProximaJob. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>