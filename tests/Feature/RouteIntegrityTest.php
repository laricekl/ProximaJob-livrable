<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Tests\TestCase;

class RouteIntegrityTest extends TestCase
{
    public function test_controller_routes_reference_existing_classes_and_methods(): void
    {
        $failures = [];

        foreach (Route::getRoutes() as $route) {
            $actionName = $route->getActionName();

            if ($actionName === 'Closure') {
                continue;
            }

            if (!str_contains($actionName, '@')) {
                if (!class_exists($actionName)) {
                    $failures[] = sprintf(
                        '[%s] %s -> classe introuvable %s',
                        implode('|', $route->methods()),
                        $route->uri(),
                        $actionName
                    );
                    continue;
                }

                $reflection = new ReflectionClass($actionName);

                if (!$reflection->hasMethod('__invoke')) {
                    $failures[] = sprintf(
                        '[%s] %s -> méthode __invoke introuvable sur %s',
                        implode('|', $route->methods()),
                        $route->uri(),
                        $actionName
                    );
                }

                continue;
            }

            [$controllerClass, $method] = explode('@', $actionName, 2);

            if (!class_exists($controllerClass)) {
                $failures[] = sprintf(
                    '[%s] %s -> controleur introuvable %s',
                    implode('|', $route->methods()),
                    $route->uri(),
                    $controllerClass
                );
                continue;
            }

            $reflection = new ReflectionClass($controllerClass);

            if (!$reflection->hasMethod($method)) {
                $failures[] = sprintf(
                    '[%s] %s -> méthode introuvable %s@%s',
                    implode('|', $route->methods()),
                    $route->uri(),
                    $controllerClass,
                    $method
                );
            }
        }

        $this->assertSame([], $failures, implode(PHP_EOL, $failures));
    }
}
