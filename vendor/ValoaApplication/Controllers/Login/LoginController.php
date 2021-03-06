<?php
/**
 * The Initial Developer of the Original Code is
 * Tarmo Alexander Sundström <ta@sundstrom.im>
 *
 * Portions created by the Initial Developer are
 * Copyright (C) 2014 Tarmo Alexander Sundström <ta@sundstrom.im>
 *
 * All Rights Reserved.
 *
 * Contributor(s):
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace ValoaApplication\Controllers\Login;

use Libvaloa\Auth\Auth;
use Libvaloa\Controller\Redirect;

use Webvaloa\Configuration;

use RuntimeException;
use stdClass;

class LoginController extends \Webvaloa\Application
{

    private $backend;

    public function __construct()
    {
        $this->ui->addCSS("/css/Login.css");

        $this->backend = \Webvaloa\config::$properties['webvaloa_auth'];
    }

    public function index()
    {
        if (isset($_SESSION["UserID"]) && !empty($_SESSION["UserID"])) {
            Redirect::to(\Webvaloa\config::$properties['default_controller_authed']);
        }

        $config = new Configuration;
        $this->view->config = new stdClass;

        // Custom branding
        if ($config->webvaloa_branding) {
            $this->view->config->webvaloa_branding = $config->webvaloa_branding;
        }

        // Custom branding
        if ($config->enable_registration) {
            $this->view->config->enable_registration = $config->enable_registration;
        }

        // Site name
        if ($config->site_name) {
            $this->view->config->site_name = $config->site_name;
        }
    }

    public function login()
    {
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            try {
                $auth = new Auth;
                $auth->setAuthenticationDriver(new $this->backend);

                if (!$auth->authenticate($_POST["username"], $_POST["password"])) {
                    throw new RuntimeException("Authentication failed.");
                }
            } catch (RuntimeException $e) {
                $this->ui->addError($e->getMessage());
            }
        }

        Redirect::to("login");
    }

}
