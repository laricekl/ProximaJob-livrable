<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewRefactorGuardTest extends TestCase
{
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
}
