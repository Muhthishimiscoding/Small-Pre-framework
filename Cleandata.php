<?php
namespace MuhthishimisCoding\PreFramework;

class CleanData
{
    /**
     * Fastest way of cleaning data with a little violation of dry principle
     */
    static function cleanPostdata(array &$data, Database &$db, $usetrim = 1, $useEscape = 1, $usehtmlEntities = 1)
    {
        if ($usetrim && $useEscape && $usehtmlEntities) {

            return array_map(fn($e) => htmlentities(trim($db->pdo->quote(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS)))), $data);

        } elseif ($usetrim && $useEscape) {

            return array_map(fn($e) => trim($db->pdo->quote(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS))), $data);

        } elseif ($usetrim && $usehtmlEntities) {

            return array_map(fn($e) => trim(htmlentities(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS))), $data);

        } elseif ($useEscape && $usehtmlEntities) {
            return array_map(fn($e) => htmlentities($db->pdo->quote(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS))), $data);

        } elseif ($usetrim) {
            return array_map(fn($e) => trim(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS)), $data);

        } elseif ($useEscape) {

            return array_map(fn($e) => $db->pdo->quote(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS)), $data);

        } elseif ($usehtmlEntities) {

            return array_map(fn($e) => htmlentities(filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS)), $data);

        }
    }
}

// $cleanData = $data;
// if ($usetrim) {
//     $cleanData = array_map(fn($e) => trim($e), $cleanData);
// }
// if ($useEscape) {
//     $cleanData = array_map(fn($e) => $db->pdo->quote($e), $cleanData);
// }
// if ($usehtmlEntities) {
//     $cleanData = array_map(fn($e) => htmlentities($e), $cleanData);
// }
// return $cleanData;