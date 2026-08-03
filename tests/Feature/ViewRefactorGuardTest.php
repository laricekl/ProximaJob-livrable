<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewRefactorGuardTest extends TestCase
{
    public function test_active_views_do_not_use_native_browser_dialogs(): void
    {
        $views = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                resource_path('views'),
                \FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($views as $view) {
            if ($view->getExtension() !== 'php') {
                continue;
            }

            $content = file_get_contents($view->getPathname());
            $usesNativeDialog = preg_match(
                '/(?<!ProximaUI\.)(?<!->)(?<!::)\b(?:window\.)?(?:alert|confirm|prompt)\s*\(/',
                $content
            );

            $this->assertSame(
                0,
                $usesNativeDialog,
                sprintf('%s utilise encore une boîte native du navigateur.', $view->getPathname())
            );
        }
    }

    public function test_active_views_do_not_extend_legacy_bootstrap_layouts(): void
    {
        $views = [
            'resources/views/dashboard.blade.php',
            'resources/views/notifications/index.blade.php',
            'resources/views/profile/edit.blade.php',
            'resources/views/cv/personalization-form.blade.php',
            'resources/views/cv/preview.blade.php',
            'resources/views/user/infos-cv.blade.php',
            'resources/views/user/details.blade.php',
            'resources/views/user/historique-candidatures.blade.php',
            'resources/views/user/historique-candidatures-ia.blade.php',
            'resources/views/user/profil-public.blade.php',
            'resources/views/user/detail-candidature.blade.php',
            'resources/views/user/abonnement.blade.php',
            'resources/views/user/planabonnement.blade.php',
            'resources/views/entreprise/create-offre.blade.php',
            'resources/views/entreprise/offres-disponibles.blade.php',
            'resources/views/entreprise/historique.blade.php',
            'resources/views/entreprise/candidature-ia.blade.php',
            'resources/views/entreprise/liste-candidature.blade.php',
            'resources/views/entreprise/abonne.blade.php',
            'resources/views/entreprise/promotion-entreprise.blade.php',
            'resources/views/entreprise/details-candidat.blade.php',
        ];

        $legacyLayouts = [
            "layouts.app",
            "layouts.connected_app",
            "layouts.entreprise_app",
            "layouts.dashboard",
        ];

        foreach ($views as $viewPath) {
            $content = file_get_contents(base_path($viewPath));

            $this->assertNotFalse($content, sprintf('Impossible de lire %s', $viewPath));

            foreach ($legacyLayouts as $layout) {
                $this->assertStringNotContainsString(
                    "@extends('{$layout}')",
                    $content,
                    sprintf('%s ne doit plus étendre %s', $viewPath, $layout)
                );

                $this->assertStringNotContainsString(
                    "@extends(\"{$layout}\")",
                    $content,
                    sprintf('%s ne doit plus étendre %s', $viewPath, $layout)
                );
            }
        }
    }

    public function test_shared_interface_exposes_consistent_loading_and_motion_states(): void
    {
        $script = file_get_contents(public_path('js/proxima-ui.js'));
        $styles = file_get_contents(public_path('css/proxima-glass.css'));

        $this->assertStringContainsString('proxima-navigation-progress', $script);
        $this->assertStringContainsString('proximaBusy', $script);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $styles);
        $this->assertStringContainsString('.proxima-button-spinner', $styles);
    }

    public function test_cv_preview_has_loading_error_and_retry_states(): void
    {
        $view = file_get_contents(resource_path('views/user/infos-cv.blade.php'));

        $this->assertStringContainsString('id="generatedCvLoading"', $view);
        $this->assertStringContainsString('id="generatedCvError"', $view);
        $this->assertStringContainsString('id="generatedCvRetry"', $view);
        $this->assertStringContainsString('setCvFrameSource(', $view);
    }

    public function test_custom_dialog_traps_focus_and_animates_dismissal(): void
    {
        $view = file_get_contents(resource_path('views/partials/proxima-dialogs.blade.php'));

        $this->assertStringContainsString("event.key !== 'Tab'", $view);
        $this->assertStringContainsString("overlay.classList.add('is-open')", $view);
        $this->assertStringContainsString('proxima-toast-message', $view);
    }
}
