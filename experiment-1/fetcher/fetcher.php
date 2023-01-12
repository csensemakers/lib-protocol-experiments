<?php
/* Copyright (C) 2023 Stephan Kreutzer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3 or any later version,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License 3 for more details.
 *
 * You should have received a copy of the GNU Affero General Public License 3
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * @author Stephan Kreutzer
 * @since 2023-01-12
 */

$sources = array();
// Using an associative array for source index to allow for gaps in the
// future, if some of the sources go offline or non-compatible.
$sources[1] = "https://example.org";

foreach ($sources as $index => $url)
{
    if (file_exists("./".$index) == true)
    {
        unlink("./".$index);
    }

    /** @todo This loads all the data into memory! */
    $data = file_get_contents($url);
    file_put_contents("./".$index, $data);
}

?>
