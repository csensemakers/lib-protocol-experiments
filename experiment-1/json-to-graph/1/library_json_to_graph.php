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
 * @attention This method reads all of the input into memory.
 * @author Stephan Kreutzer
 * @since 2023-01-11
 */

$input = file_get_contents("./input.json");
$input = json_decode($input);

$nodeIdCurrent = 1;

echo "node ".$nodeIdCurrent." \"".$input->url."\"\n";
$nodeIdCurrent++;

$nodeIdCurrent = handleLists($input->lists, $nodeIdCurrent - 1, $nodeIdCurrent);



/** @todo Nothing done for passing $lists per reference! */
function handleLists($lists, $nodeIdParent, $nodeIdCurrent)
{
    for ($listIndex = 0, $listMax = count($lists);
         $listIndex < $listMax;
         $listIndex++)
    {
        /** @todo No escaping yet! */
        echo "node ".$nodeIdCurrent." \"".($lists[$listIndex]->name)."\"\n".
             "edge ".$nodeIdCurrent." ".$nodeIdParent."\n";
        $nodeIdCurrent++;

        $nodeIdCurrent = handleBooks($lists[$listIndex]->books, $nodeIdCurrent - 1, $nodeIdCurrent);
    }

    return $nodeIdCurrent;
}

/** @todo Nothing done for passing $books per reference! */
function handleBooks($books, $nodeIdParent, $nodeIdCurrent)
{
    for ($bookIndex = 0, $bookMax = count($books);
         $bookIndex < $bookMax;
         $bookIndex++)
    {
        /** @todo No escaping yet! */
        echo "node ".$nodeIdCurrent." \"".($books[$bookIndex]->title)."\"\n".
             "edge ".$nodeIdCurrent." ".$nodeIdParent."\n";
        $nodeIdCurrent++;
    }

    return $nodeIdCurrent;
}

?>
