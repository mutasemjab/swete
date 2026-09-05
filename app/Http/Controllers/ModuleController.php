<?php

namespace App\Http\Controllers;

/**
 * Base class for all ERP module controllers.
 *
 * Each module controller must declare its module key, which must match
 * a key in config/modules.php.  The moduleView() helper automatically
 * injects the current module data so Blade layouts can build the sidebar.
 *
 * Usage:
 *   class UserController extends ModuleController
 *   {
 *       protected string $module = 'users';
 *
 *       public function index() {
 *           return $this->moduleView('users.index', compact('users'));
 *       }
 *   }
 */
abstract class ModuleController extends Controller
{
    protected string $module = '';

    protected function moduleView(string $view, array $data = [])
    {
        return view($view, array_merge($data, [
            'currentModule'       => $this->module,
            'currentModuleConfig' => config("modules.{$this->module}"),
        ]));
    }
}
