<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte entreprise validé - Proximalob</title>
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
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .success-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }
        .success-icon {
            font-size: 50px;
            margin-bottom: 15px;
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
        .button:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }
        .footer { 
            text-align: center; 
            padding: 25px; 
            font-size: 14px; 
            color: #666; 
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .company-box {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #059669;
            margin: 20px 0;
        }
        .company-box h3 {
            margin: 0 0 8px 0;
            color: #059669;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        .login-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
        }
        .login-info h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 16px;
            font-weight: 600;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
            display: inline-block;
            min-width: 80px;
        }
        .info-value {
            color: #2d3748;
        }
        .info-link {
            color: #059669;
            text-decoration: none;
            font-weight: 500;
        }
        .info-link:hover {
            text-decoration: underline;
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
        .benefits {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .benefits h3 {
            margin: 0 0 15px 0;
            color: #854d0e;
            font-size: 16px;
            font-weight: 600;
        }
        .benefits ul {
            margin: 0;
            padding-left: 20px;
        }
        .benefits li {
            margin-bottom: 10px;
            color: #713f12;
        }
        .benefits li strong {
            color: #854d0e;
        }
        .tip-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .tip-box p {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }
        .secondary-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #6b7280;
        }
        .secondary-link a {
            color: #059669;
            text-decoration: none;
            font-weight: 500;
        }
        .secondary-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête avec succès -->
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>Compte entreprise validé !</h1>
            <div class="success-badge">Félicitations</div>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <div class="greeting">Bonjour {{ $userName }},</div>
            
            <div class="message">
                <p>Excellente nouvelle ! Nous avons le plaisir de vous informer que votre compte entreprise a été <strong>validé avec succès</strong> par notre équipe d'administration.</p>
            </div>

            <!-- Informations de l'entreprise -->
            <div class="company-box">
                <h3>Entreprise validée</h3>
                <div class="company-name">{{ $companyName }}</div>
            </div>

            <div class="message">
                <p>Votre profil d'entreprise est maintenant <strong>actif</strong> et vous avez accès à toutes les fonctionnalités de la plateforme Proximalob.</p>
            </div>
 

            <div class="tip-box">
                <p>💡 <strong>Conseil :</strong> Utilisez votre mot de passe habituel pour vous connecter</p>
            </div>

            <!-- Liste des avantages -->
            <div class="benefits">
                <h3>🎯 Vous pouvez désormais :</h3>
                <ul>
                    <li><strong>Publier des offres d'emploi</strong> et atteindre des milliers de candidats qualifiés</li>
                    <li><strong>Gérer vos annonces</strong> depuis votre tableau de bord entreprise</li>
                    <li><strong>Consulter les candidatures</strong> et entrer en contact avec les talents</li>
                    <li><strong>Mettre à jour votre profil</strong> et présenter votre entreprise</li>
                    <li><strong>Accéder aux statistiques</strong> de vos offres d'emploi</li>
                </ul>
            </div>

            <!-- Bouton d'action principal -->
            <div style="text-align: center;">
                <a href="proximajob.com/login" class="button">Se connecter maintenant</a>
            </div>

          

            <!-- Message de support -->
            <div class="message" style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
                <p style="color: #6b7280; font-size: 14px;">
                    Si vous avez des questions ou besoin d'assistance, n'hésitez pas à 
                    <a href="mailto:{{ config('mail.from.address') }}" style="color: #059669; text-decoration: none; font-weight: 500;">contacter notre support</a>.
                </p>
            </div>

            <div class="message">
                <p style="font-size: 15px;">Merci de votre confiance et bienvenue sur <strong>Proximalob</strong> !</p>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                Cordialement,<br>
                <strong style="color: #059669;">L'équipe Proximalob</strong>
            </p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p style="margin: 0 0 5px 0;"><strong>Proximalob</strong></p>
            <p style="margin: 0 0 15px 0; color: #059669; font-weight: 500;">La plateforme de recrutement de proximité</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} Proximalob. Tous droits réservés.</p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #9ca3af;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>