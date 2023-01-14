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
 * @since 2023-01-13
 */


class GraphMerger
{
    function __construct()
    {
        $this->mysqlConnection = mysqli_connect("localhost", "root", "");

        if ($this->mysqlConnection != false)
        {
            if (mysqli_query($this->mysqlConnection, "USE graph") == false)
            {
                mysqli_close($this->mysqlConnection);
                $this->mysqlConnection = null;

                throw new Exception("Database not found.");
            }
        }
        else
        {
            $this->mysqlConnection = null;

            throw new Exception("Can't connect to database.");
        }
    }

    function insertNode($idExternal, $value)
    {
        if ($this->mysqlConnection == null)
        {
            throw new Exception("No connection to the database.");
        }

        if (array_key_exists($idExternal, $this->idMapping) == true)
        {
            throw new Exception("Duplicate node ID in the source.");
        }

        $node = mysqli_query($this->mysqlConnection,
                             "SELECT `id`\n".
                             "FROM `node`\n".
                             "WHERE `value` LIKE '".mysqli_real_escape_string($this->mysqlConnection, $value)."'");

        if ($node != false)
        {
            $result = mysqli_fetch_assoc($node);
            mysqli_free_result($node);
            $node = $result;
        }

        $idInternal = null;

        if (is_array($node) != true)
        {
            /** @todo This and other operations would be faster, if not a
              * separate query per node, but only one with a series of values. */
            if (mysqli_query($this->mysqlConnection,
                             "INSERT INTO `node` (`id`,\n".
                             "    `value`)\n".
                             "VALUES (NULL,\n".
                             "    '".mysqli_real_escape_string($this->mysqlConnection, $value)."')\n") !== true)
            {
                throw new Exception("Node insert operation failed.");
            }

            $idInternal = mysqli_insert_id($this->mysqlConnection);

            if ($idInternal === 0 ||
                $idInternal === null)
            {
                throw new Exception("Node insertion operation was without effect.");
            }
        }
        else
        {
            $idInternal = (int)($node["id"]);
        }

        $this->idMapping[$idExternal] = $idInternal;
    }

    function insertEdge($idSource, $idTarget)
    {
        if ($this->mysqlConnection == null)
        {
            throw new Exception("No connection to the database.");
        }

        /** @todo Implement something to support temporary IDs for nodes not
          * yet read/imported/known in the source, if source is unordered. */

        if (array_key_exists($idSource, $this->idMapping) == true)
        {
            $idSource = $this->idMapping[$idSource];
        }
        else
        {
            throw new Exception("Edge references an unknown node source ID in the source.");
        }

        if (array_key_exists($idTarget, $this->idMapping) == true)
        {
            $idTarget = $this->idMapping[$idTarget];
        }
        else
        {
            throw new Exception("Edge references an unknown node target ID in the source.");
        }

        $edge = mysqli_query($this->mysqlConnection,
                             "SELECT `id`\n".
                             "FROM `edge`\n".
                             "WHERE `source`=".((int)$idSource)." AND\n".
                             "    `target`=".((int)$idTarget));

        if ($edge != false)
        {
            $result = mysqli_fetch_assoc($edge);
            mysqli_free_result($edge);
            $edge = $result;
        }

        if (is_array($edge) != true)
        {
            if (mysqli_query($this->mysqlConnection,
                             "INSERT INTO `edge` (`id`,\n".
                             "    `source`,\n".
                             "    `target`)\n".
                             "VALUES (NULL,\n".
                             "    ".((int)$idSource).",\n".
                             "    ".((int)$idTarget).")\n") !== true)
            {
                throw new Exception("Node insert operation failed.");
            }
        }
        else
        {
            // Edge exists already.
        }
    }

    protected $mysqlConnection = null;
    protected $idMapping = array();
}


?>
