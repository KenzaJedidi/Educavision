<?php

namespace App\Service;

use App\Entity\Candidature;
use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private string $fromAddress = 'no-reply@educavision.com',
        private string $fromName = 'EducaVision'
    ) {}

    private function from(): Address
    {
        return new Address($this->fromAddress, $this->fromName);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(Utilisateur $user, string $token): void
    {
        $resetUrl = $this->urlGenerator->generate(
            'app_reset_password',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from($this->from())
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('🔐 Réinitialisation de votre mot de passe - EducaVision')
            ->html($this->buildPasswordResetHtml($user, $resetUrl));

        $this->mailer->send($email);
    }

    /**
     * Send teacher CV accepted email (account approved by admin)
     */
    public function sendTeacherCvAcceptedEmail(Utilisateur $teacher, Candidature $candidature): void
    {
        $loginUrl = $this->urlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from($this->from())
            ->to(new Address($teacher->getEmail(), $teacher->getFullName()))
            ->subject('✅ Votre candidature enseignant a été acceptée - EducaVision')
            ->html($this->buildTeacherAcceptedHtml($teacher, $candidature, $loginUrl));

        $this->mailer->send($email);
    }

    /**
     * Send teacher CV rejected email
     */
    public function sendTeacherCvRejectedEmail(Utilisateur $teacher, Candidature $candidature): void
    {
        $email = (new Email())
            ->from($this->from())
            ->to(new Address($teacher->getEmail(), $teacher->getFullName()))
            ->subject('❌ Votre candidature enseignant - EducaVision')
            ->html($this->buildTeacherRejectedHtml($teacher, $candidature));

        $this->mailer->send($email);
    }

    /**
     * Send account banned email
     */
    public function sendAccountBannedEmail(Utilisateur $user): void
    {
        $banUntil = $user->getBanUntil()?->format('d/m/Y à H:i') ?? 'indéterminé';
        $reason = $user->getBanReason() ?? 'Non précisée';

        $email = (new Email())
            ->from($this->from())
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('⚠️ Votre compte EducaVision a été suspendu')
            ->html($this->buildBannedHtml($user, $banUntil, $reason));

        $this->mailer->send($email);
    }

    // ============================================================
    // HTML Templates
    // ============================================================

    private function buildPasswordResetHtml(Utilisateur $user, string $resetUrl): string
    {
        $name = htmlspecialchars($user->getFullName());
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:40px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
      <!-- Header -->
      <tr><td style="background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;">
        <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">🎓 EducaVision</h1>
        <p style="color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;">Plateforme d'apprentissage en ligne</p>
      </td></tr>
      <!-- Body -->
      <tr><td style="padding:40px 48px;">
        <h2 style="color:#1a1a2e;font-size:22px;margin:0 0 16px;">Réinitialisation du mot de passe</h2>
        <p style="color:#555;font-size:15px;line-height:1.7;">Bonjour <strong>{$name}</strong>,</p>
        <p style="color:#555;font-size:15px;line-height:1.7;">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
        <div style="text-align:center;margin:32px 0;">
          <a href="{$resetUrl}" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);">
            🔐 Réinitialiser mon mot de passe
          </a>
        </div>
        <div style="background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;">
          <p style="color:#92400e;margin:0;font-size:14px;">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
        </div>
        <p style="color:#999;font-size:13px;margin-top:24px;">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
          <a href="{$resetUrl}" style="color:#667eea;word-break:break-all;">{$resetUrl}</a>
        </p>
      </td></tr>
      <!-- Footer -->
      <tr><td style="background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;">
        <p style="color:#aaa;font-size:12px;margin:0;">© 2026 EducaVision — Tous droits réservés</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }

    private function buildTeacherAcceptedHtml(Utilisateur $teacher, Candidature $candidature, string $loginUrl): string
    {
        $name = htmlspecialchars($teacher->getFullName());
        $offreTitle = $candidature->getOffreStage()
            ? htmlspecialchars($candidature->getOffreStage()->getTitre())
            : 'votre candidature';
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:40px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
      <tr><td style="background:linear-gradient(135deg,#11998e,#38ef7d);padding:40px;text-align:center;">
        <div style="font-size:56px;margin-bottom:12px;">✅</div>
        <h1 style="color:#ffffff;margin:0;font-size:26px;font-weight:700;">Candidature Acceptée !</h1>
        <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:15px;">EducaVision - Espace Enseignant</p>
      </td></tr>
      <tr><td style="padding:40px 48px;">
        <p style="color:#555;font-size:16px;line-height:1.7;">Bonjour <strong>{$name}</strong>,</p>
        <p style="color:#555;font-size:15px;line-height:1.7;">Excellente nouvelle ! Votre candidature pour le poste <strong>« {$offreTitle} »</strong> a été <span style="color:#11998e;font-weight:700;">acceptée</span> par notre équipe après examen de votre CV.</p>
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:20px 24px;margin:24px 0;">
          <p style="color:#166534;margin:0;font-size:15px;">🎉 Vous pouvez maintenant accéder à votre espace enseignant et commencer à créer vos cours.</p>
        </div>
        <div style="text-align:center;margin:32px 0;">
          <a href="{$loginUrl}" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(17,153,142,0.4);">
            🎓 Accéder à mon espace enseignant
          </a>
        </div>
        <p style="color:#777;font-size:14px;line-height:1.6;">En cas de questions, n'hésitez pas à contacter notre équipe. Bienvenue dans la famille EducaVision !</p>
      </td></tr>
      <tr><td style="background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;">
        <p style="color:#aaa;font-size:12px;margin:0;">© 2026 EducaVision — Tous droits réservés</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }

    private function buildTeacherRejectedHtml(Utilisateur $teacher, Candidature $candidature): string
    {
        $name = htmlspecialchars($teacher->getFullName());
        $offreTitle = $candidature->getOffreStage()
            ? htmlspecialchars($candidature->getOffreStage()->getTitre())
            : 'votre candidature';
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:40px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
      <tr><td style="background:linear-gradient(135deg,#f093fb,#f5576c);padding:40px;text-align:center;">
        <div style="font-size:56px;margin-bottom:12px;">📋</div>
        <h1 style="color:#ffffff;margin:0;font-size:26px;font-weight:700;">Candidature examinée</h1>
        <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:15px;">EducaVision - Espace Enseignant</p>
      </td></tr>
      <tr><td style="padding:40px 48px;">
        <p style="color:#555;font-size:16px;line-height:1.7;">Bonjour <strong>{$name}</strong>,</p>
        <p style="color:#555;font-size:15px;line-height:1.7;">Après examen attentif de votre CV pour le poste <strong>« {$offreTitle} »</strong>, nous sommes au regret de vous informer que votre candidature n'a pas été retenue pour le moment.</p>
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;padding:20px 24px;margin:24px 0;">
          <p style="color:#991b1b;margin:0;font-size:14px;">Cela ne reflète pas nécessairement votre valeur professionnelle. Nous vous encourageons à repostuler pour d'autres opportunités.</p>
        </div>
        <p style="color:#777;font-size:14px;line-height:1.6;">Merci pour l'intérêt que vous portez à EducaVision. Nous conservons votre profil pour de futures opportunités.</p>
      </td></tr>
      <tr><td style="background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;">
        <p style="color:#aaa;font-size:12px;margin:0;">© 2026 EducaVision — Tous droits réservés</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }

    private function buildBannedHtml(Utilisateur $user, string $banUntil, string $reason): string
    {
        $name = htmlspecialchars($user->getFullName());
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:40px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
      <tr><td style="background:linear-gradient(135deg,#f7971e,#ffd200);padding:40px;text-align:center;">
        <div style="font-size:56px;margin-bottom:12px;">⚠️</div>
        <h1 style="color:#1a1a2e;margin:0;font-size:26px;font-weight:700;">Compte suspendu</h1>
        <p style="color:rgba(26,26,46,0.75);margin:8px 0 0;font-size:15px;">Notification EducaVision</p>
      </td></tr>
      <tr><td style="padding:40px 48px;">
        <p style="color:#555;font-size:16px;line-height:1.7;">Bonjour <strong>{$name}</strong>,</p>
        <p style="color:#555;font-size:15px;line-height:1.7;">Votre compte EducaVision a été temporairement <strong>suspendu</strong> par l'administrateur.</p>
        <div style="background:#fefce8;border:1px solid #fde68a;border-radius:12px;padding:20px 24px;margin:24px 0;">
          <p style="color:#854d0e;margin:0 0 8px;font-size:15px;"><strong>📅 Suspendu jusqu'au :</strong> {$banUntil}</p>
          <p style="color:#854d0e;margin:0;font-size:15px;"><strong>📝 Raison :</strong> {$reason}</p>
        </div>
        <p style="color:#777;font-size:14px;line-height:1.6;">Si vous pensez qu'il s'agit d'une erreur, veuillez contacter notre équipe d'administration.</p>
      </td></tr>
      <tr><td style="background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;">
        <p style="color:#aaa;font-size:12px;margin:0;">© 2026 EducaVision — Tous droits réservés</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}
