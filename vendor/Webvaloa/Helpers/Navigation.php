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

namespace Webvaloa\Helpers;

use Libvaloa\Db;

use stdClass;

class Navigation
{

    public function __construct()
    {

    }

    public function get()
    {
        $db = \Webvaloa\Webvaloa::DBConnection();

        $navi = new stdClass;
        $navi->sub = array();

        $query = '
            SELECT id, parent_id, type, target_id, translation
            FROM structure
            WHERE (locale = ? OR locale = ?)
            ORDER BY parent_id, ordering ASC';

        $stmt = $db->prepare($query);
        $stmt->set(\Webvaloa\Webvaloa::getLocale())->set($tmp = '*');

        try {
            foreach ($stmt->execute() as $row) {
                $navi->sub[$row->id] = $row;

                if (!is_null($row->parent_id)) {
                    $navi->sub[$row->parent_id]->sub[] = $row;
                }

            }

            foreach ($navi->sub as $k => $v) {
                if (!is_null($v->parent_id)) {
                    unset($navi->sub[$k]);
                }
            }

            return $navi;
        } catch (Exception $e) {

        }
    }

}
