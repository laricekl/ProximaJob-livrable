import { execFileSync } from 'node:child_process';

function runLaravelExpression(expression) {
  const bootstrap = [
    "require 'vendor/autoload.php';",
    "$app = require 'bootstrap/app.php';",
    "$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();",
    expression,
  ].join(' ');

  return execFileSync('php', ['-r', bootstrap], {
    cwd: process.cwd(),
    encoding: 'utf8',
  }).trim();
}

export function getVerificationToken(email) {
  const safeEmail = email.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

  return runLaravelExpression(
    `echo App\\Models\\EmailVerification::where('email', '${safeEmail}')->latest('created_at')->value('token') ?? '';`,
  );
}

export function getUserStatus(email) {
  const safeEmail = email.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

  return runLaravelExpression(
    `$user = App\\Models\\User::where('email', '${safeEmail}')->first(); echo $user ? ($user->status ?? '') . '|' . ($user->email_verified_at ? 'verified' : 'unverified') : '';`,
  );
}
