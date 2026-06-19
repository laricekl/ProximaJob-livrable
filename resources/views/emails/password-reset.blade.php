<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 12px 12px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">ProximaJob</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0;">Réinitialisation de mot de passe</p>
    </div>

    <div style="background: white; padding: 40px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
        <h2 style="color: #333; margin-bottom: 20px;">Bonjour !</h2>
        
        <p style="margin-bottom: 20px;">
            Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" 
               style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 16px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                Réinitialiser mon mot de passe
            </a>
        </div>

        <p style="margin: 20px 0; font-size: 14px; color: #666;">
            Ce lien de réinitialisation expirera dans {{ config('auth.passwords.users.expire') }} minutes.
        </p>

        <p style="margin: 20px 0;">
            Si vous n'avez pas demandé cette réinitialisation, aucune action n'est requise.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="font-size: 12px; color: #999; margin-bottom: 10px;">
            Si vous avez des difficultés à cliquer sur le bouton "Réinitialiser mon mot de passe", 
            copiez et collez l'URL ci-dessous dans votre navigateur web :
        </p>
        
        <p style="font-size: 12px; color: #667eea; word-break: break-all;">
            {{ $url }}
        </p>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="color: #999; font-size: 12px; margin: 0;">
                © {{ date('Y') }} ProximaJob. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>