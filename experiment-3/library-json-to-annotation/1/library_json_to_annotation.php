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
 * @since 2023-01-30
 */

$input = file_get_contents("./library.json");
$input = json_decode($input);

handleLists($input->lists);


/** @todo Nothing done for passing $lists per reference! */
function handleLists($lists)
{
    $currentId = 1;

    for ($listIndex = 0, $listMax = count($lists);
         $listIndex < $listMax;
         $listIndex++)
    {
        $currentId = handleBooks($lists[$listIndex]->books, $currentId);
    }

    return 0;
}

/** @todo Nothing done for passing $books per reference! */
function handleBooks($books, $currentId)
{
    for ($bookIndex = 0, $bookMax = count($books);
         $bookIndex < $bookMax;
         $bookIndex++)
    {
        if (file_exists("./output/".$currentId.".json") == true)
        {
            echo "Error: File exists already.";
            exit(1);
        }

        $book = $books[$bookIndex];

        if (isset($book->link) != true)
        {
            echo "Book without link.\n";
            continue;
        }

        if (strlen($book->link) <= 0)
        {
            echo "Book with empty link.\n";
            continue;
        }

        if (isset($book->notes) != true)
        {
            echo "Book without notes.\n";
            continue;
        }

        if (strlen($book->notes) <= 0)
        {
            echo "Book with empty notes.\n";
            continue;
        }

        file_put_contents("./output/".$currentId.".json",
                          "{".
                              "\"annotation\":".
                              "{".
                                  "\"version\":\"0.1\",".
                                  "\"url\":".json_encode($book->link).",".
                                  "\"note\":".json_encode($book->notes).
                              "}".
                          "}");

        $currentId++;
    }

    return $currentId;
}

?>
